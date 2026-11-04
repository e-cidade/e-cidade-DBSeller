<script setup>
import { ref }  from 'vue';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
const props = defineProps(['integracao']);
const toast = useToast();
async function ativarIntegracao() {
    const data = {
        habilitar_pncp: "habilitar",
    }
    try {
        const response = await axios.post('v4/api/patrimonial/pncp/integracao/habilitar/', data);
        toast.add({severity: 'success', detail: 'Integração Concluída', summary: 'Sucesso', life: 4000});
        document.location.reload();
    } catch (e) {
        console.log(e)
    }
    toast.add({severity: 'warn', detail: response.data.data.message, summary: 'Atenção', life: 4000});
}

</script>
<template>
    <Toast/>
    <div class="flex justify-content-center mt-8" v-if="props.integracao">
        <Card style="width: 45em">
            <template #title>
                Integração
            </template>
            <template #subtitle>
                Ativar Integração
            </template>
            <template #content>
                <h3>Por favor, verifique a configuração de integração com o PNCP!</h3>
            </template>
            <template #footer>
                <div class="text-center">
                    <Button icon="pi pi-check" label="Integrar" @click="ativarIntegracao()"/>
                </div>
            </template>
        </Card>
    </div>
</template>
