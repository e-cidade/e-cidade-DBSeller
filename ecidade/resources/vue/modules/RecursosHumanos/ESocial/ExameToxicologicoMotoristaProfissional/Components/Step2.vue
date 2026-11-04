<template>
    <div>
        <label for="data" class="font-bold block mb-2">
            Data do exame
        </label>
        <InputMask
            id="data"
            class="col-5"
            v-model="localData.dataExame"
            placeholder="dd/mm/aaaa"
            mask="99/99/9999"
        />
    </div>
    <div>
        <label for="cnpjlab" class="font-bold block mb-2">
            CNPJ do Laboratório Responsável pelo exame
        </label>
        <InputMask
            id="cnpjlab"
            v-model="localData.cnpjLaboratorio"
            class="col-5"
            placeholder="99.999.999/9999-99"
            mask="99.999.999/9999-99"
        />
    </div>
    <div>
        <label for="codSeqExa" class="font-bold block mb-2">
            Código Sequencial do Exame
        </label>
        <InputText id="codSeqExam" disabled v-model="localData.seqExame" class="col-5" />
    </div>
    <div>
        <label for="nomeMed" class="font-bold block mb-2">
            Nome do Médico
        </label>
        <InputText id="nomeMed" :maxlength="70" v-model="localData.nomeMedico" class="col-5" />
    </div>
    <div>
        <label for="nrInscMed" class="font-bold block mb-2">
            CRM do Médico Responsável
        </label>
        <InputText id="nrInscMed" :maxlength="10" v-model="localData.nrInscMedico" class="col-5" />
    </div>
    <div>
        <label for="ufCrm" class="font-bold block mb-2">
            UF de Expedição do CRM
        </label>
        <Dropdown id="ufCrm" :options="opcoesUF" v-model="localData.ufSelecionada" />
    </div>
</template>

<script setup>
import InputMask from 'primevue/inputmask';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:modelValue']);

const localData = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const opcoesUF = [ null,
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
    'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
    'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
];
</script>
