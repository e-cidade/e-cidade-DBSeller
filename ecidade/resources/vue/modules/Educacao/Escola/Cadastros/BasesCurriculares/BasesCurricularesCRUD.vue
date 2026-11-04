<script setup>
import {ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import DialogFormBasesCurriculares from "./DialogFormBasesCurriculares.vue";
import ConfirmDialog from "primevue/confirmdialog";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const loading = ref(false)
const confirm = useConfirm();
const props = defineProps(['escola', 'modulo'])
const isSecretaria = props.modulo == 7159;
const baseEdicao = ref(null)
const routes = {}
routes.bases = isSecretaria ? `v4/api/educacao/secretaria/bases-curriculares/`
    : `v4/api/educacao/escola/${props.escola}/bases-curriculares/`

routes.excluir = `v4/api/educacao/escola/${props.escola}/bases-curriculares/`
const controleFrequencias = ref({'G': 'GLOBALIZADA', 'I': 'INDIVIDUAL'})
const medidaFrequencias = ref({'D': 'DIA LETIVO', 'P': 'PERIODO'})
const form = ref();
const products = ref();
const expandedRows = ref([]);
const toast = useToast();
const bases = ref()
const expandAll = () => {
    expandedRows.value = bases.value.filter((p) => p.codigo);
};
const collapseAll = () => {
    expandedRows.value = null;
};
const openForm = async () => {
    baseEdicao.value = null
    await form.value.open();
}
const atualizaLista = async (e) => {
    bases.value = (await window.axios.get(routes.bases)).data.data;
    baseEdicao.value = e.codigo != undefined ? e : null;
    await verificaEdicao()
}

const verificaEdicao = async () => {
    if (baseEdicao.value != null) {
        if (!baseEdicao.value.editada) {
            await openDialog()
        }
    }
}

const openDialog = async () => {
    setTimeout(async () => {
        await form.value.open(baseEdicao.value);
    }, 100)
}

const editar = async (data) => {
    baseEdicao.value = data
    await verificaEdicao()
}
const excluir = async (data) => {
    confirm.require({
        message: "Excluir este registro?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
            loading.value = true
            try {
                await window.axios.delete(`${routes.excluir}${data}/excluir`);
                bases.value = (await window.axios.get(routes.bases)).data.data;
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
            loading.value = false
        },
    });
}
onMounted(async () => {
    bases.value = (await window.axios.get(routes.bases)).data.data;
})
</script>
<template>
    <section style="width: 80vw; margin: 0 auto; margin-top: 20px">
        <Panel header="Bases Curriculares" >
            <div class="card">
                <DataTable v-model:expandedRows="expandedRows" :value="bases" dataKey="codigo">
                    <template #header>
                        <div class="p-fluid grid">
                            <div class="field col-12 md:col-2">
                                <Button text icon="pi pi-plus" label="Nova Base" @click="openForm" />
                            </div>
                            <div class="field col-12 md:col-2 col-offset-6">
                                <Button text icon="pi pi-plus" label="Abrir Todos" @click="expandAll" />
                            </div>
                            <div class="field col-12 md:col-2">
                                <Button text icon="pi pi-minus" label="Fechar Todos" @click="collapseAll" />
                            </div>
                        </div>
                    </template>
                    <Column expander style="width: 5rem" />
                    <Column field="descricao" header="Nome">
                    </Column>
                    <Column field="curso" header="Curso">
                        <template #body="slotProps">
                            {{ slotProps.data.curso.nome }}
                        </template>
                    </Column>
                    <Column field="etapaInicial" header="Etapa Inicial">
                        <template #body="slotProps">
                            {{ slotProps.data.etapaInicial.nome }}
                        </template>
                    </Column>
                    <Column field="etapaFinal" header="Etapa Final">
                        <template #body="slotProps">
                            {{ slotProps.data.etapaFinal.nome }}
                        </template>
                    </Column>
                    <Column field="turno" header="Turno"></Column>
                    <Column field="controleFrequencia" header="Frequência">
                        <template #body="{ data }">
                            {{ controleFrequencias[data.controleFrequencia] }}
                        </template>
                    </Column>
                    <Column>
                        <template #body="{ data }">
                            <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                            <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"></i>
                        </template>
                    </Column>
                    <template #expansion="slotProps">
                        <div class="p-3">
                            <h5>Detalhes:</h5>
                            <DataTable :value="[
                                {
                                    codigo:  slotProps.data.codigo,
                                    observacao: slotProps.data.observacao.length === 0 ? 'Sem Observação' : slotProps.data.observacao,
                                    conclusao: slotProps.data.conclusao,
                                    medidaFrequencia: medidaFrequencias[slotProps.data.medidaFrequencia],
                                    isAtiva: slotProps.data.isAtiva,
                                    regimeMatricula: slotProps.data.regimeMatricula,
                                    divisoes: slotProps.data.divisoesRegimeMatricula
                                }
                            ]">
                                <Column field="codigo" header="Codigo">
                                </Column>
                                <Column field="observacao" header="Observação" style="width: 30%">
                                    <template #body="slotProps">
                                        <Textarea :value="slotProps.data.observacao" autoResize cols="50" disabled/>
                                    </template>
                                </Column>
                                <Column field="regimeMatricula" header="Regime de Matricula">
                                    <template #body="slotProps">
                                        <span v-for="divisao in slotProps.data.divisoes">
                                            {{ divisao.nome }}
                                        </span>
                                        <span v-if="slotProps.data.divisoes.length === 0">
                                           {{ slotProps.data.regimeMatricula.nome }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="medidaFrequencia" header="Medida de Frequência">
                                </Column>
                                <Column field="conclusao" header="Conclusão">
                                    <template #body="slotProps">
                                        <i :class="{'pi pi-check text-green-500': slotProps.data.conclusao, 'pi pi-times text-red-600': !slotProps.data.conclusao }" style="font-size: 1.5rem;"></i>
                                    </template>
                                </Column>
                                <Column field="isAtiva" header="Ativa">
                                    <template #body="slotProps">
                                        <i :class="{'pi pi-check text-green-500': slotProps.data.isAtiva, 'pi pi-times text-red-600': !slotProps.data.isAtiva }" style="font-size: 1.5rem;"></i>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </template>
                </DataTable>
            </div>
        </Panel>
        <DialogFormBasesCurriculares ref="form" :escola="props.escola" :secretaria="isSecretaria" :base="baseEdicao" :bases="bases" @close="atualizaLista"></DialogFormBasesCurriculares>
        <ConfirmDialog/>
        <ModalLoading :isLoading="loading"/>
    </section>
</template>
<style scoped>
i {
    cursor: pointer
}
</style>
