<script setup>
import {onMounted, ref} from 'vue';
import ModalLoading from '../../Components/ModalLoading.vue';
import {useToast} from 'primevue/usetoast';

const loading = ref(false);
const visivel = ref(props.visible)
const toast = useToast();
const props = defineProps({
    visible: {Type: Boolean, required: true},
    filtros: {Type: Object, required: false}
});
const emits = defineEmits(['update:visible', 'linhaSelecionada']);
const contratos = ref([]);
const rotas = {
    buscarContratos: '/v4/api/patrimonial/contratos/consulta/acordos'
}
const form = ref({
    sequencial: {
        field: 'ac16_sequencial',
        value: null,
        label: 'Código',
    },
    ano: {
        field: 'ac16_anousu',
        value: null,
        label: 'Ano',
        disabled: false
    },
    numero: {
        field: 'ac16_numero',
        value: null,
        label: 'Número',
        disabled: false
    },
    situacao: {
        field: 'ac16_acordosituacao',
        data: [
            {
                descricao: 'ATIVO',
                codigo: 1
            },
            {
                descricao: 'RESCINDIDO',
                codigo: 2
            },
            {
                descricao: 'CANCELADO',
                codigo: 3
            },
            {
                descricao: 'HOMOLOGADO',
                codigo: 4
            },
            {
                descricao: 'PARALISADO',
                codigo: 5
            },
        ],
        label: 'Situação',
        disabled: false,
        value: null,
    },
    buscar: {
        label: 'Buscar',
        disabled: false
    },
    limpar: {
        label: 'Limpar',
        disabled: false
    }
});

function selecionarLinha(dadosLinha) {
    emits('linhaSelecionada', dadosLinha)
    visivel.value = false;
}

function limparCampos() {
    Object.values(form.value).forEach(field => {
        field.value = null
    });
}

function montarParametrosContrato() {
    const parametros = {};
    parametros.ac16_sequencial = form.value.sequencial.value;
    parametros.ac16_anousu = form.value.ano.value;
    parametros.ac16_numero = form.value.numero.value;
    parametros.ac16_acordosituacao = form.value.situacao.value;

    const filtros = props.filtros;
    if (filtros) {
        Object.keys(filtros).forEach(filtroKey => {
            parametros[filtroKey] = filtros[filtroKey]

            const campoEmTela = Object.keys(form.value).find(key => form.value[key].field === filtroKey);
            if (campoEmTela) {
                form.value[campoEmTela].disabled = true;
            }
        });
    }

    return parametros;
}

async function buscarContratos() {
    loading.value = true;

    try {
        const parametros = montarParametrosContrato();
        const response = await window.axios.post(rotas.buscarContratos, parametros);

        if (response.data.data) {
            contratos.value = response.data.data;
        }
    } catch (e) {
        toast.add({
            summary: 'Erro: ',
            detail: `Houve uma falha ao buscar os dados (${e.response.status})`,
            severity: 'error',
            life: 4000
        });
    }

    loading.value = false;
}

onMounted(() => {
    buscarContratos();
});

</script>

<template>
    <ModalLoading :isLoading="loading"/>

    <Dialog
        modal
        header="Pesquisa de Contratos"
        class="p-dialog-maximized"
        v-model:visible="visivel"
        @update:visible="val => emits('update:visible', val)"
    >
        <section class="mt-5" style="width: 70vw; margin: 0 auto">
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2">
                    <span class="p-float-label">
                        <InputNumber
                            v-model="form.sequencial.value"
                            :disabled="form.sequencial.disabled"
                            :use-grouping="false"
                        />
                        <label for="">{{ form.sequencial.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-2">
                    <span class="p-float-label">
                        <InputMask
                            v-model="form.ano.value"
                            mask="9999"
                            :disabled="form.ano.disabled"
                            :use-grouping="false"
                        />
                        <label for="">{{ form.ano.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-2">
                    <span class="p-float-label">
                        <InputText
                            v-model="form.numero.value"
                            :disabled="form.numero.disabled"
                        />
                        <label for="">{{ form.numero.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-2">
                    <span class="p-float-label">
                        <Dropdown
                            v-model="form.situacao.value"
                            option-value="codigo"
                            option-label="descricao"
                            :options="form.situacao.data"
                            :disabled="form.situacao.disabled"
                        />
                        <label for="">{{ form.situacao.label }}</label>
                    </span>
                </div>
            </div>

            <div class="p-fluid grid">
                <div class="field md:col-10"></div>

                <div class="field md:col-1">
                    <span class="p-float-label">
                        <Button
                            class="p-button"
                            icon="pi pi-eraser"
                            @click="limparCampos"
                            :label="form.limpar.label"
                        />
                    </span>
                </div>

                <div class="field md:col-1">
                    <span class="p-float-label">
                        <Button
                            class="p-button"
                            icon="pi pi-search"
                            @click="buscarContratos"
                            :label="form.buscar.label"
                        />
                    </span>
                </div>
            </div>
        </section>

        <DataTable class="mt-5" paginator tableStyle="width: 70vw; margin: 0 auto" :value="contratos" :rows="10"
                   :rows-per-page-options="[5, 10, 15, 20]" :totalRecords="contratos.length">
            <Column field="ac16_sequencial" sortable header="Código" class="w-1">
                <template #body="{ data }">
                    <span @click="selecionarLinha(data)">{{ data.ac16_sequencial }}</span>
                </template>
            </Column>
            <Column field="ac16_numero" sortable header="Número" class="w-1">
                <template #body="{ data }">
                    <span @click="selecionarLinha(data)">{{ data.ac16_numero }}</span>
                </template>
            </Column>
            <Column field="ac16_anousu" sortable header="Ano" class="w-1">
                <template #body="{ data }">
                    <span @click="selecionarLinha(data)">{{ data.ac16_anousu }}</span>
                </template>
            </Column>
            <Column field="objeto" header="Objeto" class="w-5">
                <template #body="{ data }">
                    <span @click="selecionarLinha(data)">{{ data.ac16_resumoobjeto }}</span>
                </template>
            </Column>
            <Column field="ac16_acordosituacao" header="Situação" class="w-2">
                <template #body="{ data }">
                    <div @click="selecionarLinha(data)">
                        <span v-if="data.ac16_acordosituacao === 1">ATIVO</span>
                        <span v-else-if="data.ac16_acordosituacao === 2">RESCINDIDO</span>
                        <span v-else-if="data.ac16_acordosituacao === 3">CANCELADO</span>
                        <span v-else-if="data.ac16_acordosituacao === 4">HOMOLOGADO</span>
                        <span v-else-if="data.ac16_acordosituacao === 5">PARALISADO</span>
                        <span v-else>{{ data.ac16_acordosituacao }}</span>
                    </div>
                </template>
            </Column>
            <template #empty>
                <div class="flex justify-content-center">
                    <p>Nenhum registro encontrado.</p>
                </div>
            </template>
        </DataTable>
    </Dialog>
</template>
<style scoped>
span {
    cursor: pointer;
}
</style>
