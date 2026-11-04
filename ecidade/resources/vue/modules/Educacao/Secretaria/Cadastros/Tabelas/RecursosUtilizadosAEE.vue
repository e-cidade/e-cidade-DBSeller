<script setup>

    import { ref, reactive } from "vue";
    import { useToast } from "primevue/usetoast"
    import ModalLoading from "../../../../Components/ModalLoading.vue";
    import { tsExternalModuleReference } from "@babel/types";
    import { useConfirm } from "primevue/useconfirm";
    import ConfirmDialog from "primevue/confirmdialog";

    const confirm = useConfirm();
    const props = defineProps(['escola'])

    const routes = {
        buscarRecursos: `v4/api/educacao/escola/alunoAtendimentosEspecial/busca-recursos`,
        salvarRecursos: `v4/api/educacao/escola/alunoAtendimentosEspecial/salva-recursos`,
        excluirRecursos: `v4/api/educacao/escola/alunoAtendimentosEspecial/exclui-recursos`,
        editarRecursos: `v4/api/educacao/escola/alunoAtendimentosEspecial/edita-recursos`
    }

    const form = ref({
        slctRecursos: {
            data: {
                descricao: null,
                codigo: null
            },
            label: 'Código',
            required: true
        }
    })

    const loading = ref(false)
    const optionsRecursos = reactive({
        value: [
            {
                codigo : null,
                descricao: null
            }
        ]
    })
    const toast = useToast()
    const showSalvar = ref(true)



    function buscarRecursos() {

        try {
            loading.value = true
            window.axios.get(routes.buscarRecursos).then(retorno => {
                optionsRecursos.value = retorno.data.data.map(recurso => {
                    return {
                        codigo: recurso.ed198_codigo,
                        descricao: recurso.ed198_descricao
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    const incluir = async () => {
        try {
            loading.value = true
            let parametros = {};

            parametros.ed198_descricao = form.value.slctRecursos.data.descricao === null ? ''
                                       : form.value.slctRecursos.data.descricao;
            let resposta = (await window.axios.post(`${routes.salvarRecursos}`, parametros));

            form.value.slctRecursos.data.codigo = []
            form.value.slctRecursos.data.descricao = []
            buscarRecursos()
            loading.value = false

            toast.add({
                        severity: 'success',
                        summary: 'Sucesso',
                        detail: `Sucesso ao Incluir`,
                        life: 5000
                    });
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }

    }

    function cancelar() {
        form.value.slctRecursos.data.codigo = []
        form.value.slctRecursos.data.descricao = []
    }

    const excluirRegistro = async (data) => {

        confirm.require({
            message: "Excluir este registro?",
            header: "Confirmação",
            icon: "pi pi-exclamation-triangle",
            accept: async () => {
                loading.value = true
                try {
                    let resposta = (await window.axios.delete(`${routes.excluirRecursos}/${data.codigo}`));
                    buscarRecursos();
                    loading.value = false;
                    toast.add({
                                severity: 'success',
                                summary: 'Sucesso',
                                detail: `Sucesso ao Excluir`,
                                life: 5000
                            });
                    return true;
                } catch (e) {
                    loading.value = false
                    toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: 'Prezado Usuário, não é possível excluir o item, pois o mesmo possui vínculo'
                                + ' com registros',
                        life: 5000
                    });
                }
                loading.value = false
            },
        });
    }

    function editarRegistro (data) {
        form.value.slctRecursos.data.codigo = data.codigo;
        form.value.slctRecursos.data.descricao = data.descricao;
        showSalvar.value = false;
    }

    const editar = async () => {

        try {
            loading.value = true
            let parametros = {};

            parametros.ed198_codigo = form.value.slctRecursos.data.codigo === null ? ''
                                    : form.value.slctRecursos.data.codigo;
            parametros.ed198_descricao = form.value.slctRecursos.data.descricao === null ? ''
                                       : form.value.slctRecursos.data.descricao;
            let resposta = (await window.axios.post(`${routes.editarRecursos}`, parametros));

            form.value.slctRecursos.data.codigo = []
            form.value.slctRecursos.data.descricao = []
            buscarRecursos()
            loading.value = false
            toast.add({
                        severity: 'success',
                        summary: 'Sucesso',
                        detail: `Sucesso ao Editar`,
                        life: 5000
                    });
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    buscarRecursos()

</script>

<template>
    <section class="container">
        <Panel header="Recursos Utilizados AEE">
            <div class="flex align-items-center justify-content-center mt-4">
                <span class="p-float-label">
                    <InputText id="codigo" v-model="form.slctRecursos.data.codigo" disabled/>
                    <label for="codigo">Código</label>
                </span>
            </div>
            <div class="flex align-items-center justify-content-center mt-4">
                <span class="p-float-label">
                    <InputText id="descricao" v-model="form.slctRecursos.data.descricao"
                    class="w-20rem" autoResize="true"/>
                    <label for="descricao">Descrição</label>
                </span>
            </div>
            <div class="flex align-items-center justify-content-center mt-4">
                <div class="field col-12 md:col-1"> </div>
                    <div class="field col-12 md:col-2">
                        <Button v-show="showSalvar" class="p-button" icon="pi pi-check" label="Incluir" type="submit"
                                @click="incluir()" :disabled="(form.slctRecursos.data.descricao === null ||
                                form.slctRecursos.data.descricao.length === 0)"></Button>
                        <Button v-show="!showSalvar" class="p-button" icon="pi pi-check" label="Salvar"
                                @click="editar()" :disabled="(form.slctRecursos.data.descricao === null ||
                                form.slctRecursos.data.descricao.length === 0)"></Button>
                    </div>
                    <div class="field col-12 md:col-2">
                        <Button class="p-button" icon="pi pi-times" label="Cancelar"
                                @click="cancelar"></Button>
                    </div>

            </div>
        </Panel>
        <br>
        <Fieldset legend="Tabela de Recursos Utilizados" :toggleable="true" style="width: auto; margin: 0 auto; height: auto">
            <div class="p-fluid grid">
                <DataTable  v-model="expandedRows" :value="optionsRecursos.value"
                            style="width: 100%" :loading="loading" class="tableRecursos">

                    <template #empty>
                        Nenhum registro foi encontrado
                    </template>
                    <Column field="codigo" header="Código" >
                        <template #body="slotProps">
                            {{ slotProps.data.codigo }}
                        </template>
                    </Column>
                    <Column field="descricao" header="Recursos utilizados" >
                        <template #body="slotProps">
                            {{ slotProps.data.descricao}}
                        </template>
                    </Column>
                    <Column header="Ações"  >
                        <template #body="slotProps">
                            <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editarRegistro(slotProps.data)"></i>
                            <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluirRegistro(slotProps.data)"></i>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </Fieldset>
        <ConfirmDialog/>
        <ModalLoading :isLoading="loading"/>
    </section>
</template>

<style scoped>
    :deep(.p-fieldset-legend) {
        padding: 0!important;
        border: none;
        background: transparent;
    }
    :deep(.p-fieldset){
        background: #e1dede;
    }
    .tableRecursos :deep(.p-datatable-tbody > tr > td) {
        height: 1px;
    }

</style>
