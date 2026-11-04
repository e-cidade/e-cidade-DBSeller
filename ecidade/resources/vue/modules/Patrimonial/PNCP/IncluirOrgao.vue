<script setup>
import { ref } from "vue";
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import ModalLoading from "../../Components/ModalLoading";

const cnpj = ref();
const verifica = ref ({
    cnpj: false,
    poderSelecionado: false,
    razaoSocial: false,
    esferaSelecionada: false
});
const loading = ref(false);
const razaoSocial = ref();
const poderSelecionado = ref();
const toast = useToast();
const poder = ref([
    {name:'Selecione', codigo:'0'},
    {name:'Legislativo', codigo:'L'},
    {name:'Executivo', codigo: 'E'},
    {name:'Judiciário', codigo: 'J'},
    {name:'Não se Aplica', codigo: 'N'},
]);
const esferaSelecionada = ref();
const esfera = ref([
    {name:'Selecione', codigo:'0'},
    {name:'Federal', codigo: 'F'},
    {name:'Estadual', codigo: 'E'},
    {name:'Municipal', codigo: 'M'},
    {name:'Distrital', codigo: 'D'},
    {name:'Não se Aplica', codigo: 'N'},
]);
function limparCampos() {
    cnpj.value = '';
    razaoSocial.value = '';
    poderSelecionado.value = '0';
    esferaSelecionada.value = '0'
}
async function incluir() {
    if (!verificaCampos()) {
        return false;
    }

    try {
        const data = {
            cnpj: cnpj.value,
            razaoSocial: razaoSocial.value,
            poder: poderSelecionado.value,
            esfera: esferaSelecionada.value
        }
        loading.value = true;
        await axios.post('v4/api/patrimonial/pncp/integracao/orgao/', data);
        toast.add({severity: 'success', detail: 'Órgão cadastrado com sucesso.', summary: 'Atenção', life: 4000});
        limparCampos();
    } catch (e) {
        toast.add({severity: 'warn', detail: e.response.data.message, summary: 'Atenção', life: 4000});
    }
    loading.value = false;
}
function verificaCampos() {
    verifica.value.cnpj = false;
    verifica.value.razaoSocial = false;
    verifica.value.poderSelecionado = false;
    verifica.value.esferaSelecionada = false;
    if (!cnpj.value) {
        verifica.value.cnpj = true;
        const message = 'Preencha o CNPJ';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false
    }
    if (!razaoSocial.value) {
        verifica.value.razaoSocial = true;
        const message = 'Preencha a Razão Social';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false;
    }
    if (poderSelecionado.value === '0' || !poderSelecionado.value) {
        verifica.value.poderSelecionado = true;
        const message = 'Selecione o Poder';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false;
    }
    if (esferaSelecionada.value === '0' || !esferaSelecionada.value) {
        verifica.value.esferaSelecionada = true;
        const message = 'Selecione a Esfera';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false;
    }
    return true;
}
</script>
<template>
    <ModalLoading :is-loading="loading"/>
    <section>
        <Panel class="container" header="Órgão">
            <div class="p-fluid grid formgrid">
                <div class="field col-12 md:col-6">
                    <label for="cnpj" class="required">CNPJ</label>
                    <InputMask id="cnpj" mask="99.999.999/9999-99" v-model="cnpj" :class="{'p-invalid': verifica.cnpj}"/>
                </div>
                <div class="field col-12 md:col-6">
                    <label for="razaoSocial" class="required">Razão Social</label>
                    <InputText
                        id="razaoSocial"
                        type="text"
                        v-model="razaoSocial"
                        :class="{'p-invalid': verifica.razaoSocial}"
                        maxlength="100"
                    />
                </div>
                <div class="field col-12 md:col-6">
                    <label for="catalogoMaterialServico" class="required">Poder</label>
                    <Dropdown
                        :class="{'p-invalid': verifica.poderSelecionado}"
                        v-model="poderSelecionado"
                        :options="poder"
                        optionLabel="name"
                        optionValue="codigo"
                        placeholder="Selecione"
                    />
                </div>
                <div class="field col-12 md:col-6">
                    <label for="catalogoMaterialServico" class="required">Esfera</label>
                    <Dropdown
                        :class="{'p-invalid': verifica.esferaSelecionada}"
                        v-model="esferaSelecionada"
                        :options="esfera"
                        optionLabel="name"
                        optionValue="codigo"
                        placeholder="Selecione"
                    />
                </div>
            </div>

        </Panel>
    </section>
    <section>
        <div class="text-center">
            <Button label="Incluir" icon="pi pi-save" class="p-button-primary" @click="incluir()"/>
        </div>
    </section>
    <Toast />
</template>
