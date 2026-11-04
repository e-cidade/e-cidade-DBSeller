<template>
    <div class="container">
        <ModalLoading :isLoading="carregando" :message="'Salvando dados.'"/>

        <form class="col-12">
            <div class="container">
                <fieldset>
                    <legend>
                        {{ props.steps[step] }}
                    </legend>
                    <div>
                        <label for="" class="font-bold block mb-2">
                            Empregador
                        </label>
                        <DropdownInstituicao @change="selectInstit" :instit="instituicao" />
                    </div>
                    <component
                        :is="props.form[step]"
                        v-model="stepData[step]"
                    ></component>
                    <div class="flex justify-content-end">
                        {{ step + 1 }} de {{ props.form.length }}
                    </div>
                </fieldset>
                <Toast />
            </div>
            <DialogExames
                v-model="dialogExamesVisible"
                :instituicao="instituicao.value"
                @exame-selecionado="onExameSelecionado"
            />
            <div class="flex justify-content-center flex-wrap gap-3">
                <div>
                    <Button  @click="NovoExame">Novo</Button>
                </div>
                <div>
                    <Button v-if="step !== 0" @click="step--">Voltar</Button>
                </div>
                <div>
                    <Button @click="openDialog">Pesquisar</Button>
                </div>
                <div>
                    <Button v-if="step !== props.form.length - 1" :disabled="proximoDisabled" @click="step++">Próximo</Button>
                </div>
                <div>
                    <Button v-if="step === props.form.length - 1" :disabled="salvarDisabled" @click="enviarEvento">Salvar</Button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>

import Button from 'primevue/button';
import DialogExames from './DialogExames'
import {useToast} from 'primevue/usetoast';
import { ref,computed } from 'vue';
import ModalLoading from "../../../../Components/ModalLoading"
import DropdownInstituicao from "@modules/Configuracao/Instituicao/Components/DropdownInstituicao";

const step = ref(0);
const props = defineProps(['instituicao', 'form', 'steps']);
const instituicao = ref(props.instituicao);
const toast = useToast();
const sequencial = ref(null);
const dialogExamesVisible = ref(false);
const carregando = ref(false);

const proximoDisabled = computed(() => {
    if (step.value === 0) {
        const data = stepData.value[0];
        return !data.cpf || !data.matricula;
    }
    return false;
});
// Reactive data array for each step
const stepData = ref([
    {},
    {}
]);

const salvarDisabled = computed(() => {
    const combinedData = Object.assign({}, ...stepData.value);

    // Campos obrigatórios
    const camposObrigatorios = [
        'cpf',
        'matricula',
        'dataExame',
        'cnpjLaboratorio',
        'nomeMedico',
    ];

    // Verifica se algum campo obrigatório está vazio
    return camposObrigatorios.some(field => !combinedData[field]);
});

const selectInstit = (data) => {
    instituicao.value = data.codigo;
};

const enviarEvento = async () => {
    const combinedData = Object.assign({}, ...stepData.value);

    // Mapeando os campos do front-end para os campos do back-end
    const payload = {
        eso41_sequencial: sequencial.value || null,
        eso41_cpftrab: combinedData.cpf ? combinedData.cpf.replace(/\D/g, '') : null,
        eso41_matricula: combinedData.matricula,
        eso41_dtexame: combinedData.dataExame ? formatDate(combinedData.dataExame) : null,
        eso41_cnpjlab: combinedData.cnpjLaboratorio ? combinedData.cnpjLaboratorio.replace(/\D/g, '') : null,
        eso41_codseqexame: combinedData.seqExame || null,
        eso41_nmmed: combinedData.nomeMedico || null,
        eso41_nrcrm: combinedData.nrInscMedico || null,
        eso41_ufcrm: combinedData.ufSelecionada || null,
        eso41_instit: instituicao.value,
    };
    carregando.value = true;
    await axios.post('v4/api/recursos-humanos/e-social/exame-toxicologico/store', payload)
        .then(response => {
            toast.add({ severity: 'success', summary: 'Sucesso', detail: response.data.message, life: 3000 });
            if (!sequencial.value ) {
                sequencial.value = response.data.data.data.eso41_sequencial

                stepData.value[1].seqExame = response.data.data.data.eso41_codseqexame;
            }
        })
        .catch(error => {
            toast.add({ severity: 'error', summary: 'Erro', detail: 'Ocorreu um erro ao salvar os dados.', life: 3000 });
        });
    carregando.value = false;

};

const formatDate = (dateString) => {
    const parts = dateString.split('/');
    if (parts.length === 3) {
        // Assume que o formato é dd/mm/aaaa
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }
    return null;
};

const openDialog = () => {
    dialogExamesVisible.value = true;
};

const onExameSelecionado = (dadosExame) => {
    stepData.value[0].cpf = dadosExame.eso41_cpftrab;
    stepData.value[0].matricula = dadosExame.eso41_matricula;

    stepData.value[1].dataExame = dadosExame.eso41_dtexame ? formatDateToInput(dadosExame.eso41_dtexame) : null;
    stepData.value[1].cnpjLaboratorio = dadosExame.eso41_cnpjlab;
    stepData.value[1].seqExame = dadosExame.eso41_codseqexame;
    stepData.value[1].nomeMedico = dadosExame.eso41_nmmed;
    stepData.value[1].nrInscMedico = dadosExame.eso41_nrcrm;
    stepData.value[1].ufSelecionada = dadosExame.eso41_ufcrm;

    sequencial.value = dadosExame.eso41_sequencial;

    step.value = 0;
};


const formatDateToInput = (dateString) => {
    const parts = dateString.split('-');
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return null;
};

const NovoExame = () => {
    sequencial.value = null;
    stepData.value = [
        {},
        {}
    ];

    step.value = 0;

    toast.add({ severity: 'info', summary: 'Novo Exame', detail: 'Formulário de novo exame iniciado.', life: 3000 });
};

</script>
