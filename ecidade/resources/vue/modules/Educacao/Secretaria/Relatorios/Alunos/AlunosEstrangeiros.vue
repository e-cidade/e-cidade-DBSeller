<script setup>
import { ref } from "vue";
import { useToast } from "primevue/usetoast";
import MultiDownload from "../../../../Components/MultiDownload.vue";

const routes = {
    escolas: `v4/api/educacao/escola/`,
    emissao: `v4/api/educacao/secretaria/relatorios/alunos/alunos-estrangeiros`
}
const toast = useToast();
const download = ref(null)


const form = ref({
    slctAnosLetivos: {
        data: null,
        label: 'Ano',
        disabled: false,
        required: false
    },
    slctEscolas: {
        data: null,
        label: 'Escola',
        disabled: false,
        required: false
    },
    fileType: []
})
const optionsAnosLetivos = ref(null)
const optionsEscolas = ref([])

function getEscolas() {
    try {
        optionsEscolas.value = [{
            name: 'TODAS',
            code: 0
        }]
        window.axios.get(routes.escolas).then(response => {
            response.data.data.forEach(escola => {
                optionsEscolas.value.push({
                    name: escola.ed18_c_nome.trim(),
                    code: escola.ed18_i_codigo
                })
            })
        })
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
    }
}

function hideMultiDownload() {
    window.location.reload()
}

function getAnosLetivos(anoInicio) {
    let anos = [];
    let currentYear = new Date().getFullYear()
    for (let i = anoInicio; i <= currentYear; i++) {
        anos.push({
            name: `${i}`,
            code: i
        })
    }
    return anos;
}
function emitir() {
    let parametros = {};
    parametros.escolas = form.value.slctEscolas.data.code === 0 ?
        optionsEscolas.value.filter(opcao => opcao.code !== 0).map(escola => escola.code) :
        [form.value.slctEscolas.data.code]

    parametros.ano = form.value.slctAnosLetivos.data.code
    parametros.tipo = form.value.fileType
    try {
        window.axios.post(routes.emissao, parametros).then(response => {
            response.data.data.forEach(relatorio => {
                download.value.addFile(
                    `${relatorio.pathExterno}`,
                    `${relatorio.name}`
                )
            })
            download.value.openModal();
        })
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
    }
}

optionsAnosLetivos.value = getAnosLetivos(2019);
getEscolas();
</script>

<template>
    <section class="container">
        <Panel header="Relatório de Alunos Estrangeiros" style="width: 600px; margin: 0 auto">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2">
                </div>
                <div class="field col-12 md:col-2">
                    <span class="p-float-label">
                        <Dropdown id="slctAnosLetivos"
                                  v-model="form.slctAnosLetivos.data"
                                  :options="optionsAnosLetivos"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctAnosLetivos.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-6">
                    <span class="p-float-label">
                        <Dropdown id="slctAnosLetivos"
                                  v-model="form.slctEscolas.data"
                                  :options="optionsEscolas"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctEscolas.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-2">
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-3">
                </div>
                <div class="field col-12 md:col-3">
                    <div class="field-checkbox">
                        <Checkbox inputId="csv" name="csv" value="csv" v-model="form.fileType" />
                        <label for="csv"><i class="pi pi-file-excel" style="font-size: 1.5rem"></i> CSV</label>
                    </div>
                </div>
                <div class="field col-12 md:col-3">
                    <div class="field-checkbox">
                        <Checkbox inputId="pdf" name="pdf" value="pdf" v-model="form.fileType" />
                        <label for="pdf"><i class="pi pi-file-pdf" style="font-size: 1.5rem"></i> PDF</label>
                    </div>
                </div>
                <div class="field col-12 md:col-3">
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4">
                </div>
                <div class="field col-12 md:col-4">
                    <Button class="p-button" label="Emitir" icon="pi pi-print"
                            @click.prevent="emitir"
                            :disabled="form.slctEscolas.data === null ||
                            form.slctAnosLetivos.data === null ||
                            form.fileType.length === 0"></Button>
                </div>
                <div class="field col-12 md:col-4">
                </div>
            </div>
        </Panel>
    </section>
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" @hide="hideMultiDownload" ref="download"/>
</template>
<style scoped>

</style>
