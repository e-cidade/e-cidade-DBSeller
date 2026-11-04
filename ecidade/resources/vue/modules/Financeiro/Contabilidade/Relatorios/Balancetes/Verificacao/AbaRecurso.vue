<script setup>
import {computed, onMounted, ref} from 'vue';
import {FilterMatchMode} from "primevue/api";
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

const props = defineProps(['modelValue', 'exercicio', 'dataSistema']);
const emit = defineEmits(['update:modelValue'])

const recursos = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const metaKey = ref(true);
const recursosGrid = ref([]);

const classificacaoSelecionada = ref(null);
const opcoesClassificacoes = ref([]);

const filters = ref({global: { value: null, matchMode: FilterMatchMode.CONTAINS }})

const buscaClassificacoes = async () => {
    const response = await window.axios.get('v4/api/financeiro/orcamento/classificacao/com-siconfi');

    for (const classificacao of response.data.data) {
        opcoesClassificacoes.value.push({
            name: classificacao.descricao,
            value: classificacao.id
        });
    }
}

const buscaRecursos = async () => {
    const response = await window.axios.get(`v4/api/financeiro/orcamento/recursos/${props.exercicio}`);

    for (const recurso of response.data.data) {
        recursosGrid.value.push(recurso);
    }
}

const selecionar = () => {
    let recursosSelecionar = [];

    for (const classificacao of classificacaoSelecionada.value) {
        let x = recursosGrid.value.filter((item) => {
            return item.classificacaofr_id === classificacao;
        });

        recursosSelecionar = recursosSelecionar.concat(x)
    }

    recursos.value = recursosSelecionar;
}

function recursoInativo(recurso) {
    return props.dataSistema > (new Date(recurso.o15_datalimite + 'T00:00:00'));
}

onMounted(() => {
    buscaClassificacoes();
    buscaRecursos();
});

</script>

<template>

    <section class="flex flex-column w-full gap-2">

        <section class="flex justify-content-center">
            <div class="formgrid grid mt-4 gap-2 md:gap-0">
                <div class="field col-12">
                    <SelectButton v-model="classificacaoSelecionada" :options="opcoesClassificacoes"
                                  optionLabel="name" optionValue="value"
                                  multiple aria-labelledby="multiple"
                                  @click="selecionar"/>
                </div>
            </div>
        </section>

        <section class="flex justify-content-center w-full">
            <div class="card w-full">
                <DataTable v-model:selection="recursos" :value="recursosGrid" dataKey="o15_codigo"
                           v-model:filters="filters" class="w-full"
                           :globalFilterFields="['codigo_siconfi','gestao', 'o15_recurso', 'descricao']">
                    <template #header>
                        <div class="flex justify-content-end">
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Pesquisar" />
                            </IconField>
                        </div>
                    </template>
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column sortable field="codigo_siconfi" header="Siconfi"></Column>
                    <Column sortable field="gestao" header="Gestão"></Column>
                    <Column sortable field="o15_recurso" header="Subrecurso"></Column>
                    <Column sortable field="descricao" header="Nome">
                        <template #body="slotProps">
                            <Tag v-if="recursoInativo(slotProps.data)" value="INATIVO" severity="danger"/>
                            {{ slotProps.data.descricao }}
                        </template>
                    </Column>
                    <Column field="lbComplemento" header="Complemento">
                        <template #body="slotProps">
                            {{ `${slotProps.data.o200_sequencial.toString().padStart(4, '0')} - ${slotProps.data.o200_descricao}` }}
                        </template>
                    </Column>
                </DataTable>
            </div>
        </section>

    </section>
</template>

<style scoped>

</style>
