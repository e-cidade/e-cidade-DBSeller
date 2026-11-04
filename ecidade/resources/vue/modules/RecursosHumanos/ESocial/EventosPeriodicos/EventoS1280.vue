<script setup>
import {onMounted, ref, watch} from "vue";
import ButtonActionVue from "./components/ButtonsVue.vue";
import DropdownVue from "./components/DropdownGrupo.vue";
import StepScreen from "./components/StepScreen.vue";
import EmpregadorSelected from "./components/EmpregadorSelected.vue";
import Grupo3Selected from "./components/Grupo3Selected.vue";
import ModalLoading from '../../../Components/ModalLoading'
import {FilterMatchMode} from 'primevue/api';
import {useToast} from "primevue/usetoast";

const toast = useToast();
//VARIAVEL DE CONTROLLE DO SKELLETON
const isLoading = ref(false);
//VARIAVEIS DE CONTROLE PARA HABILITAR OU DESABILITAR INPUTS DO TIPO CHECKBOX NO COMPONENTE FILHO BUTTONSVUE
const validaCheckStep1 = ref(false)
const validaCheckStep2 = ref(false)
const validaCheckStep5 = ref(false)
//VARIAVEIS DE CONTROLE PARA HABILITAR OU DESABILITAR INPUTS DO TIPO TEXT NO COMPONENTE FILHO BUTTONSVUE
const validaInputStep1 = ref(false)
const validaInputStep2 = ref(false)
const validaInputStep3 = ref(false)
const validaInputStep4 = ref(false)
//VARIAVEL PARA CONTROLLAR O BOTAO DA TELA INICIAL
const btnDisabled = ref(true);
//VARIAVEIS DE CONTROLE DA TROCA DE TELA
const currentStep = ref(0);
const totalSteps = ref(5);

const selectedCategory = ref(0);
const maskCategory = ref(1);
const indSubstPatr = ref()
const percRedContrib = ref()
const percTransf = ref()
const instituicao = ref();
const infoComplementares = ref()
const tpInsc = ref('')
const cgm = ref();
const sequencial = ref();
const filters = ref({
    'eso40_periodo': {value: null, matchMode: FilterMatchMode.STARTS_WITH}
});

const periodosValue = ref();
const lotacaoTributaria = ref()
const fatorMes = ref()
const fator13 = ref()

const visible = ref(false);

//VARIAVEL QUE RECEBE AS MENSAGENS DE ERRO
const errors = ref()

//VARIAVEL PARA CONTROLLAR O VALOR DO CHANGE DA PRIMEIRA TELA
const selectedPeriodo = ref({key: 1});

const selectPeriodoEmit = ref()

const baseURL = "v4/api/recursos-humanos/e-social";

const periodos = ref([
    {name: "Mensal (AAAA-MM)", key: "1"},
    {name: "Anual (AAAA)", key: "2"},
]);


const categories = ref([
    {name: "CNPJ", key: "1"},
    {
        name: "CAEPF (Cadastro de Atividade Econômica de Pessoa Física)",
        key: "2",
    },
]);

const patronal = ref([
    {name: "1 - Integralmente substituída", key: "1"},
    {name: "2 - Parcialmente substituída", key: "2"},
]);

const contribuicao = ref([
    {name: "1 - 0,2000", key: "1"},
    {name: "2 - 0,4000", key: "2"},
    {name: "3 - 0,6000", key: "3"},
    {name: "4 - 0,8000", key: "4"},
    {name: "5 - 1,0000", key: "5"},
]);


//STEP 0
watch(periodosValue, () => {
    errors.value = ''
    btnDisabled.value = false
    const datas = periodosValue.value.split('-')
    const dataAtual = new Date()
    const validaAno = dataAtual.getFullYear()
    const validaAnoSec = parseInt(validaAno) - parseInt(datas[0])
    if (parseInt(validaAnoSec) > 150 || datas[0] > validaAno) {
        errors.value = 'Ano inválido'
        btnDisabled.value = true
    }

    if (selectedPeriodo.value.key == 1) {
        const meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']
        const mesesValidos = meses.includes(datas[1])

        if (!mesesValidos) {
            errors.value = 'Mês inválido'
            btnDisabled.value = true
            return false
        }

        const mesAnoRegex = new RegExp(/^\d{4}-\d{2}$/)
        const regex = mesAnoRegex.test((datas[0] + '-' + datas[1]))
        if (!regex) {
            errors.value = 'Erro de formato de data'
            btnDisabled.value = true
            return false
        }
    } else if (selectedPeriodo.value.key == 2) {

        const mesAnoRegex = new RegExp(/^\d{4}$/)
        const regex = mesAnoRegex.test(datas[0])
        if (!regex) {
            errors.value = 'Erro de formato de data'
            btnDisabled.value = true
            return false
        }
    }
})
//FIM STEP 0

//STEP 1
watch(tpInsc, () => {
    if (tpInsc.value && tpInsc.value.length > 0) {
        validaInputStep1.value = true
        return
    }
    if (tpInsc.value == '') {
        validaInputStep1.value = false
        return
    }
})

watch(selectedCategory, () => {
    if (selectedCategory.value && selectedCategory.value.length > 0) {
        selectedCategory.value == 2 ? maskCategory.value = 2 : maskCategory.value = 1
        validaCheckStep1.value = true
        if (selectedCategory.value == 2) {
            validaInputStep2.value = true

        }
        return
    } else {
        validaCheckStep1.value = false
        return
    }
});

//FIM STEP 1

//STEP 2
watch(indSubstPatr, () => {
    if (indSubstPatr.value && indSubstPatr.value.length > 0) {
        validaCheckStep2.value = true
    } else {
        validaCheckStep2.value = false
    }
})

watch(percRedContrib, () => {
    if (percRedContrib.value && percRedContrib.value.length > 0) {
        validaInputStep2.value = true
        return
    } else {
        validaInputStep2.value = false
        return
    }
})
//FIM STEP 2

//STEP 3
watch(lotacaoTributaria, () => {
    if (lotacaoTributaria.value && lotacaoTributaria.value.length > 0) {
        validaInputStep3.value = true
        return
    } else {
        validaInputStep3.value = false
        return
    }
})
//FIM STEP 3

//STEP 4
watch(fatorMes, () => {
    if (fatorMes.value && fatorMes.value !== '') {
        validaInputStep4.value = true
        return
    } else {
        validaInputStep4.value = false
        return
    }
})

watch(fator13, () => {
    if (fator13.value && fator13.value !== '') {
        validaInputStep4.value = true
        return
    } else {
        validaInputStep4.value = false
        return
    }
})
//FIM STEP 4

//STEP 5
watch(percTransf, () => {
    if (percTransf.value && percTransf.value.length > 0) {
        validaCheckStep5.value = true
        return
    } else {
        validaCheckStep5.value = false
        return
    }
})

//FIM STEP 5


function nextStep() {
    if (periodosValue.value == undefined || periodosValue.value == '') {
        alert('Preencha o campo do ano')
        return
    }
    if (currentStep.value == 0) {
        buscarDadosPeriodo()
    }

    if (currentStep.value == 1) {
        if (selectedCategory.value == 0) {
            alert('Selecione o código correspondente ao tipo de inscrição.')
            return
        }

        if (tpInsc.value == '') {
            alert('Informe o número de inscrição do contribuinte.')
            return
        }

    }
    currentStep.value++;
}

const buscarDadosPeriodo = async () => {
    isLoading.value = true;
    const endpoint = "/index/" + periodosValue.value + '/' + cgm.value;
    await window.axios.get(baseURL + endpoint).
    then((response) => {
            if(response.data.data.informacao) {
                preencherCampos(response.data.data);
            }
        })
    isLoading.value = false;
}
function prevStep() {
    if (currentStep.value < 0) {
        return;
    }
    currentStep.value--;
}

const selectedDados = ref({});

const onRowSelect = (event) => {
    visible.value = false
    preencherCamposTabela(event.data);
    if (currentStep.value == 0) {
        currentStep.value ++
    }


}

const preencherCamposTabela = (dados) => {
    tpInsc.value = dados.eso40_num_insc
    selectedCategory.value = dados.eso40_tp_insc
    indSubstPatr.value = dados.eso40_ind_subst_patr
    sequencial.value = dados.eso40_sequencial;
    percRedContrib.value = dados.eso40_perc_red_contrib
    lotacaoTributaria.value = dados.eso40_cod_lotacao
    fatorMes.value = dados.eso40_fator_mes
    fator13.value = dados.eso40_fator_13
    percTransf.value = dados.eso40_perc_transf
    instituicao.value.instituicao = dados.instituicao
    periodosValue.value = dados.eso40_periodo
}

const preencherCampos = (dados) => {
    tpInsc.value = dados.informacao.eso40_num_insc
    selectedCategory.value = dados.informacao.eso40_tp_insc
    indSubstPatr.value = dados.informacao.eso40_ind_subst_patr
    sequencial.value = dados.informacao.eso40_sequencial;
    percRedContrib.value = dados.informacao.eso40_perc_red_contrib
    lotacaoTributaria.value = dados.informacao.eso40_cod_lotacao
    fatorMes.value = dados.informacao.eso40_fator_mes
    fator13.value = dados.informacao.eso40_fator_13
    percTransf.value = dados.informacao.eso40_perc_transf
    instituicao.value.instituicao = dados.instituicao
    cgm.value = dados.instituicao.numcgm
    periodosValue.value = dados.informacao.eso40_periodo
}

const dadosInfoComplementares = async () => {
    isLoading.value = true;
    const endpoint = "/index";
    await window.axios
        .get(baseURL + endpoint)
        .then((response) => {
            cgm.value = response.data.data.instituicao.numcgm;
            instituicao.value = response.data.data;
            infoComplementares.value = response.data.data.informacaoComplementar;

        })
        .catch((err) => {
            toast.add({severity: 'error',
                detail: "Ocorreu um erro ao buscar as informações",
                summary: 'Erro',
                life: 6000});

        })
        .finally(() => {
            isLoading.value = false;
        });
};


const novo = ((e) => {
    window.location.reload()
    // currentStep.value = 0
})

const validaEnvio1280 = () => {
    if (selectedCategory.value == 0) {
        alert('Selecione o código correspondente ao tipo de inscrição.')
        return false
    }

    if (tpInsc.value == '') {
        alert('Informe o número de inscrição do contribuinte.')
        return false
    }
    return true
}

const criar1280 = () => {
    if (!validaEnvio1280()) {
        return
    }
    isLoading.value = true;

    const form = {
        // 'selectedPeriodo' :selectedPeriodo.value,
        'cgm': cgm.value,
        'tpInsc': maskCategory.value,
        'tpInscNumber': tpInsc.value.replace(/\D/g, ''),
        'indSubstPatr': indSubstPatr.value,
        'percRedContrib': percRedContrib.value,
        'lotacaoTributaria': lotacaoTributaria.value,
        'fatorMes': fatorMes.value,
        'fator13': fator13.value,
        'percTransf': percTransf.value,
        'instituicao': instituicao.value.instituicao.codigo,
        'sequencial': sequencial.value,
        'periodo' : periodosValue.value
    }

    const endpoint = "/store";
    window.axios.post(baseURL + endpoint, form)
        .then((response) => {
            if (response.data.data.informacao && response.data.data.informacao.eso40_sequencial) {
                sequencial.value = response.data.data.informacao.eso40_sequencial;
            }
            toast.add({severity: 'success',
                detail: "As informações foram salvas com sucesso",
                summary: 'Sucesso',
                life: 6000});

        })
        .catch((err) => {
            toast.add({severity: 'error',
                detail: "Ocorreu um erro ao salvar as informaçoes",
                summary: 'Erro',
                life: 6000});
        })
        .finally(() => {
            isLoading.value = false;
        });

}


const alterar = () => {
    errors.value = ''
    selectedPeriodo.value === 1 ? 2 : 1
};

const showTable = (e) => {
    visible.value = e.value

}

const alterarGrupoP = (c) => {
    selectPeriodoEmit.value = ''
    lotacaoTributaria.value = ''
    if (c.value == 2) {
        validaInputStep2.value = true
        validaInputStep3.value = true
    } else {
        validaInputStep2.value = false
        validaInputStep3.value = false
        selectPeriodoEmit.value = c.value
    }
}

onMounted(async () => {
    await dadosInfoComplementares()
});
</script>

<template>
    <section class="container">
        <div class="mt-5 grid">
            <div class="card flex justify-center">
                <Dialog v-model:visible="visible" modal header="Consulta">
                    <div class="card mt-3">
                        <DataTable v-model:selection="selectedDados"
                                   v-model:filters="filters"
                                   selectionMode="single"
                                   @rowSelect="onRowSelect"
                                   dataKey="eso40_nr_insc"
                                   :value="infoComplementares"
                                   sortMode="single"
                                   paginator :rows="5"
                                   :rowsPerPageOptions="[5, 10, 20, 50]" tableStyle="min-width: 50rem">
                            <template #header>
                                <div class="container">
                                        <label for="periodo">Período: </label>
                                        <InputText id="periodo"
                                                   class="p-inputtext-sm md:w-12rem"
                                                   v-model="filters['eso40_periodo'].value"/>

                                </div>
                            </template>
                            <Column field="eso40_periodo" sortable header="Período" style="width:15%"></Column>
                            <Column field="eso40_nr_insc" header="Código do Empregador" style="width: 25%"></Column>
                            <Column field="instituicao.nomeinst" header="Empregador" style="width: 25%"></Column>
                            <template v-if="eso40_num_insc !== null">
                                <Column field="eso40_num_insc" header="CNPJ do Orgão" style="width: 25%"></Column>
                            </template>
                            <template v-else>
                                <Column field="instituicao.cgc" header="CNPJ do Orgão" style="width: 25%"></Column>
                            </template>
                        </DataTable>
                    </div>
                </Dialog>
            </div>
            <form style="height: 350px" class="col-12">
                <input type="hidden" v-model="sequencial">
                <ModalLoading :isLoading="isLoading"/>

                <template v-if="currentStep === 0">
                    <Card>
                        <template #content>
                            <h1>Indicativo de Período</h1>
                            <Divider/>
                            <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                :isLoading="isLoading"/>
                            <Divider/>

                            <div class="line">
                                <template v-if="isLoading">
                                    <Skeleton width="10rem" height="2rem" class="w-full mb-3"></Skeleton>
                                </template>
                                <template v-else>
                                    <Dropdown v-model="selectedPeriodo" :options="periodos" optionLabel="name"
                                              placeholder="Mensal(AAAA-MM)" class="w-full md:w-[14rem]"
                                              @change="alterar"/>
                                </template>
                                <Divider/>
                                <template v-if="isLoading">
                                    <Skeleton width="10rem" height="2rem" class="w-full mb-3"></Skeleton>
                                </template>
                                <template v-else>
                                    <template v-if="selectedPeriodo.key == 1">
                                        <InputMask label="Período" v-model="periodosValue" mask="9999-99"
                                                   class="w-full mb-3"/>
                                        <Message severity="error" v-if="errors">{{ errors }}</Message>
                                    </template>
                                    <template v-else>
                                        <InputMask label="Período" v-model="periodosValue" mask="9999"
                                                   class="w-full mb-3"/>
                                        <Message severity="error" v-if="errors">{{ errors }}</Message>
                                    </template>
                                </template>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <template v-if="isLoading">
                                    <Skeleton width="4rem" height="2rem"></Skeleton>
                                </template>
                                <template v-else>
                                    <ButtonActionVue :instituicao="instituicao" :periodos="periodosValue"
                                                     @showTable="showTable"
                                                     :forms="selectedDados"
                                                     :btnDisabled="btnDisabled"
                                                     :currentStep="currentStep" @nextStep="nextStep"/>
                                </template>
                            </div>
                        </template>
                    </Card>
                </template>
                <template v-if="currentStep === 1">
                    <Card>
                        <template #content>
                            <div class="line">
                                <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                    :isLoading="isLoading"/>
                                <div class="flex">
                                    <label for="" class="col-8"><b>Formulário de Cadastro para o
                                        eSocial</b></label>
                                    <StepScreen :currentStep="currentStep" :totalSteps="totalSteps"/>
                                </div>
                                <Divider/>
                                <div>
                                    <b>Grupo de Perguntas:</b>
                                </div>
                                <div>
                                    <b>Informações de identificação do estabelecimento, obra ou orgão público</b>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="line">
                                <div class="flex flex-column">
                                    <p class="m-0">
                                        <b>Preencher com o código
                                            correspondente ao tipo de inscrição
                                            conforme tabela 5</b>
                                    </p>
                                    <div v-for="category in categories" :key="category.key"
                                         class="flex items-center mt-2">
                                        <RadioButton v-model="selectedCategory" :checked="category.key"
                                                     :inputId="category.key"
                                                     name="dynamic" :value="category.key"/>
                                        <label :for="category.key" class="ml-2">{{ category.name }}</label>
                                    </div>
                                </div>
                            </div>
                            <Divider/>
                            <div class="line mt-2">
                                <label for=""><b>Informar o número de inscrição do estabelecimento, obra de construção
                                    civil ou
                                    orgão público de acordo com o tipo de inscrição.</b></label>
                                <template v-if="maskCategory == 1">
                                    <InputMask label="Período" v-model="tpInsc" mask="99.999.999/9999-99"
                                               class="w-full mb-3"/>
                                </template>
                                <template v-else>
                                    <InputMask label="Período" v-model="tpInsc" mask="999.999.999-99"
                                               class="w-full mb-3"/>
                                </template>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <ButtonActionVue :disableCheck="validaCheckStep1" :forms="selectedDados"
                                                 @showTable="showTable"
                                                 :validaInput="validaInputStep1" :currentStep="currentStep" @novo="novo"
                                                 @salvar="criar1280"
                                                 @prevStep="prevStep" @nextStep="nextStep"/>
                            </div>
                        </template>
                    </Card>
                </template>
                <template v-if="currentStep === 2">
                    <Card>
                        <template #content>
                            <div class="line">
                                <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                    :isLoading="isLoading"/>
                                <div class="flex">
                                    <label for="" class="col-8"><b>Formulário de Cadastro para o
                                        eSocial</b></label>
                                    <StepScreen :currentStep="currentStep" :totalSteps="totalSteps"/>
                                </div>
                                <Divider/>
                                <div>
                                    <b>Grupo de Perguntas:</b>
                                </div>
                                <div>
                                    <b>Grupo preenchimento exclusivamente por empresa enquadrada nos arts. 7º a 9º da
                                        Lei 12.546/2011, conforme classificação tributária indicada no evento
                                        S-1000.</b>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="line">
                                <div class="flex flex-column">
                                    <p class="m-0">
                                        <b>Indicativo de substituição de
                                            contribuição previdenciária
                                            patronal.</b>
                                    </p>
                                    <div v-for="p in patronal" :key="p.key" :selected="indSubstPatr"
                                         class="flex items-center mt-2">
                                        <RadioButton v-model="indSubstPatr" :inputId="p.key" name="dynamic"
                                                     :value="p.key"/>
                                        <label :for="p.key" class="ml-2">{{
                                                p.name
                                            }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="line mt-2">
                                <Divider/>
                                <div class="line mt-2">
                                    <label for=""><b>Informar percentual não substituído pela contribuição prevista na
                                        Lei
                                        12.546/2011.</b></label>
                                    <InputText class="w-full" :maxlength="6" v-model="percRedContrib"/>
                                </div>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <ButtonActionVue :disableCheck="true" :validaInput="validaInputStep2"
                                                 :currentStep="currentStep"
                                                 :patronal="indSubstPatr" @showTable="showTable" @novo="novo"
                                                 @salvar="criar1280" @prevStep="prevStep"
                                                 @nextStep="nextStep"/>
                            </div>
                        </template>
                    </Card>
                </template>
                <template v-if="currentStep === 3">
                    <Card>
                        <template #content>
                            <div class="line">
                                <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                    :isLoading="isLoading"/>
                                <div class="flex">
                                    <label for="" class="col-8"><b>Formulário de Cadastro para o
                                        eSocial</b></label>
                                    <StepScreen :currentStep="currentStep" :totalSteps="totalSteps"/>
                                </div>
                                <Divider/>
                                <div>
                                    <b>Grupo de Perguntas:</b>
                                </div>
                                <div>
                                    <b>Grupo preenchimento exclusivamente pelo Órgão Gestor de Mão de Obra -f
                                        OGMO(classTrib em S-1000=[09]) listando apenas seus códigos de lotação com
                                        operadores portuários enquadrados nos arts. 7º a 9º da Lei 12.546/2011.</b>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="flex flex-column">
                                <p class="m-0">
                                    <b>Informar o código atribuído pelo
                                        empregador para a lotação
                                        tributária</b>
                                </p>
                                <div class="p-inputgroup">
                                    <InputText placeholder="Requerente" :maxlength="30" class="col-12" v-model="lotacaoTributaria"/>
                                </div>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <ButtonActionVue :validaInput="validaInputStep3" :selectPeriodoEmit="selectPeriodoEmit"
                                                 :disableCheck="true" :currentStep="currentStep" @novo="novo"
                                                 @showTable="showTable"
                                                 @salvar="criar1280" @prevStep="prevStep" @nextStep="nextStep"/>
                            </div>
                        </template>
                    </Card>
                </template>
                <template v-if="currentStep === 4">
                    <Card>
                        <template #content>
                            <div class="line">
                                <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                    :isLoading="isLoading"/>
                                <div class="flex">
                                    <label for="" class="col-8"><b>Formulário de Cadastro para o
                                        eSocial</b></label>
                                    <StepScreen :currentStep="currentStep" :totalSteps="totalSteps"/>
                                </div>
                                <Divider/>
                                <div>
                                    <b>Grupo de Perguntas:</b>
                                </div>
                                <div>
                                    <b>Grupo preenchimento por empresa enquadrada no regime de tributação Simples
                                        Nacional com tributação previdenciária substituída e não substituída.</b>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="line">
                                <div class="flex flex-column">

                                    <p class="m-0">
                                        <b>Informar o fator a ser utilizado
                                            para cálculo da contribuição
                                            patronal do mês dos trabalhadores
                                            envolvidos na execução das
                                            atividades enquadradas no Anexo IV
                                            em conjunto com as dos Anexos I a
                                            III e V da Lei Complementar
                                            123/2006.</b>
                                    </p>
                                    <div class="p-inputgroup">
                                        <InputText placeholder="" :maxlength="6" v-model="fatorMes" class="col-12"/>
                                    </div>
                                    <Divider/>
                                    <p class="m-0">
                                        <b>Informar o fator a ser utilizado
                                            para cálculo da contribuição
                                            patronal do décimo terceiro dos
                                            trabalhadores envolvidos na execução
                                            das atividades enquadradas no Anexo
                                            IV em conjunto com as dos Anexos I a
                                            III e V da Lei Complementar
                                            123/2006.</b>
                                    </p>
                                    <div class="p-inputgroup">
                                        <InputText placeholder="" :maxlength="6" v-model="fator13" class="col-12"/>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <ButtonActionVue :validaInput="true" :disableCheck="true" :currentStep="currentStep"
                                                 @novo="novo"
                                                 @salvar="criar1280" @showTable="showTable" @prevStep="prevStep"
                                                 @nextStep="nextStep"/>
                            </div>
                        </template>
                    </Card>
                </template>
                <template v-if="currentStep === 5">
                    <Card>
                        <template #content>
                            <div class="line">
                                <EmpregadorSelected label="Escolha um empregador" :instituicao="instituicao"
                                                    :isLoading="isLoading"/>
                                <div class="flex">
                                    <label for="" class="col-8"><b>Formulário de Cadastro para o
                                        eSocial</b></label>
                                    <StepScreen :currentStep="currentStep" :totalSteps="totalSteps"/>
                                </div>
                                <Divider/>
                                <div>
                                    <b>Grupo de Perguntas:</b>
                                </div>
                                <div>
                                    <b>Grupo preenchimento por entidade que tenha se transformado em sociedade de fins
                                        lucrativos nos termos e no prazo da Lei 11.096/2005.</b>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="line">
                                <div class="flex flex-column">
                                    <p class="m-0">
                                        <b>Informe o percentual de
                                            contribuição social devida em caso
                                            de transformação em sociedade de
                                            fins lucrativos - Lei
                                            11.096/2005.</b>
                                    </p>
                                    <div v-for="c in contribuicao" :key="c.key" class="flex items-center mt-2">
                                        <RadioButton v-model="percTransf" :inputId="c.key" name="dynamic"
                                                     :value="c.key"/>
                                        <br/>
                                        <label :for="c.key" class="ml-2">{{
                                                c.name
                                            }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4 line mt-3">
                                <ButtonActionVue :disableCheck="validaCheckStep5" :validaInput="true"
                                                 :currentStep="currentStep"
                                                 @novo="novo" @showTable="showTable" @salvar="criar1280"
                                                 @prevStep="prevStep"/>
                            </div>
                        </template>
                    </Card>
                </template>
            </form>
        </div>
    </section>
</template>

