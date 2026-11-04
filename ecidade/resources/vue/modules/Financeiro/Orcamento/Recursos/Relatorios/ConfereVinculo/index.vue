<script setup>
import {ref} from "vue";
import LookupRecurso from "@modules/Financeiro/Orcamento/Components/LookupRecurso.vue";
import {useToast} from "primevue/usetoast";
import MultiDownload from "@modules/Components/MultiDownload.vue";
import ModalLoading from "@modules/Components/ModalLoading.vue";

const toast = useToast();

const props = defineProps(['exercicio', 'dataSistema']);

const recurso = ref({});
const download = ref(null)
const loading = ref(false);

const rota = 'v4/api/financeiro/orcamento/recursos/confere-vinculo';

async function imprimir() {
    if (recurso.value.id === undefined) {
        toast.add({severity: 'error', detail: 'Selecione um recurso.', summary: 'Erro'});
        return
    }

    loading.value = true;

    const parameters = {
        idRecurso : recurso.value.orctiporec_id,
        exercicio : props.exercicio,
    };

    // await window.axios.post(rota, formData).then(response => {
    await window.axios.get(rota, {'params': parameters}).then(response => {
        console.log(response)
        download.value.addFile(
            response.data.data.pdfLinkExterno,
            response.data.message
        )
        download.value.openModal();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}
</script>

<template>

    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Selecione o recurso" class="w-full md:w-11 lg:w-8 xl:w-7">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 ">
                        <LookupRecurso v-model="recurso" :pesquisaCodigo="recurso.codigo"
                                       :exercicio="exercicio" :data="dataSistema" label="Recurso:"/>
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <section class="flex justify-content-center column-gap-2">
        <Button class="shadow-4" icon="pi pi-print" @click="imprimir"/>
    </section>

    <ModalLoading :isLoading="loading" message="Aguarde..."/>
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
</template>

<style scoped>

</style>
