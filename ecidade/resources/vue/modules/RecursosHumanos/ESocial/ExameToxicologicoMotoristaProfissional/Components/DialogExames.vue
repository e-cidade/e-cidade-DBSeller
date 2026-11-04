<template>
    <Dialog v-model:visible="visible" header="Pesquisar exames Toxicológicos" :modal="true"
            :maximizable="true"
            :style="{ width: '1200px', minHeight: '300px' }">
        <ModalLoading :isLoading="carregando" />

        <div>
            <div class="flex align-items-center">

            <div>
                <label for="cpf">CPF</label>
                <InputText id="cpf" class="p-inputtext-sm"  v-model="filters['eso41_cpftrab'].value" placeholder="Digite o CPF" />
            </div>
            <div class="ml-3">
                <label for="matricula">Matrícula</label>
                <InputText id="matricula" class="p-inputtext-sm"  v-model="filters['eso41_matricula'].value" placeholder="Digite a Matrícula" />
            </div>
                <div class="ml-3">
                    <label for="Nome">Nome</label>
                    <InputText id="nome" class="p-inputtext-sm"  v-model="filters['z01_nome'].value" placeholder="Digite o nome" />
                </div>
            </div>
        </div>
        <DataTable
            :value="exames"
            selectionMode="single"
            v-model:selection="exameSelecionado"
            :paginator="true"
            sortMode="multiple"
            :rows="10"
            :filters="filters"
            responsiveLayout="scroll"
            @rowSelect="onRowSelect"
        >
            <Column field="eso41_cpftrab" sortable header="CPF" filter filterPlaceholder="Filtrar por CPF"></Column>
            <Column field="eso41_matricula" sortable header="Matrícula" filter filterPlaceholder="Filtrar por Matrícula"></Column>
            <Column field="z01_nome" sortable header="Nome" filter filterPlaceholder="Filtrar por nome"></Column>
            <Column field="eso41_dtexame" sortable header="Data do exame" filter filterPlaceholder="Filtrar por Data">
                <template #body="{data,field}">
                    {{formatDate(data[field])}}
                </template>
            </Column>
            <Column field="eso41_cnpjlab" header="CNPJ do Laboratório" filter filterPlaceholder="Filtrar por CNPJ"></Column>
        </DataTable>

    </Dialog>
</template>

<script setup>
import {ref, watch, computed, onMounted} from 'vue';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import { useToast } from 'primevue/usetoast';
import ModalLoading from "../../../../Components/ModalLoading"


const toast = useToast();
const carregando = ref(false);

const props = defineProps({
    modelValue: Boolean,
    instituicao: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'exame-selected']);

const visible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const exames = ref([]);
const exameSelecionado = ref(null);

const filters = ref({
    'eso41_cpftrab': { value: '', matchMode: 'contains' },
    'eso41_matricula': { value: '', matchMode: 'contains' },
    'z01_nome': { value: '', matchMode: 'contains' },
});

const  buscarExames = async () => {
    carregando.value = true;
    await axios.get("v4/api/recursos-humanos/e-social/exame-toxicologico/index")
        .then(response => {
            exames.value = response.data.data
        })
        .catch(error => {
            toast.add({ severity: 'warn', summary: 'Erro', detail: 'Erro ao buscar exames' });

        });
    carregando.value = false
};

const selecionarExame = () => {
    if (exameSelecionado.value) {
        emit('exame-selecionado', exameSelecionado.value);
        visible.value = false;
    }
};

const onRowSelect = () => {
    selecionarExame();
};

const onHide = () => {
    visible.value = false;
};

onMounted(() => {
    buscarExames();
})

const formatDate = (dateString) => {
    const parts = dateString.split('-');
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return null;
};

watch(visible, () => {
    if (visible.value) {
        buscarExames();
    }
})
</script>
