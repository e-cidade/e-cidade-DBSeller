<template>
    <div v-show="isLoading" class="background">
    </div>
    <div class="aviso-departamento">
        <Fieldset legend="Atenção">
            <p>
                O departamento atual não é uma escola.
            </p>
        </Fieldset>
    </div>
    <ModalLoading :isLoading="isLoading" :message="'Carregando Dados'"></ModalLoading>
</template>

<script setup>
    import { ref } from 'vue';
    import { useToast } from "primevue/usetoast";
    import ModalLoading from "../../../Components/ModalLoading.vue";

    const props = defineProps(['departamento']);
    const toast = useToast();
    const emits = defineEmits(['isEscola']);
    const isLoading = ref(false);
    const isEscola = ref(false);

    const routes = {
            escolas: `v4/api/educacao/escola/${props.departamento}`
    };

    async function verificaDepartamento() {
        try {
            isLoading.value = true;
            const response = await axios.get(routes.escolas);
            isEscola.value = response.data.data !== null;

            emits('is-escola', isEscola.value);
            isLoading.value = false;
        } catch (error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'Ocorreu um erro ao processar a solicitação',
                life: 10000
            });
            isLoading.value = false;
        }
    }

    verificaDepartamento();

</script>
<style lang="scss" scoped>
    .aviso-departamento {
        background-color: #E0E0E0;
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .aviso-departamento fieldset {
        width: 30vw;
        margin: 3rem auto;
    }

    fieldset {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #E0E0E0;
        border-radius: 0.6rem;
    }

    fieldset p {
        font-size: 1.2rem;
    }

    .background {
        background-color: #E0E0E0;
        width: 100vw;
        height: 100vh;
    }
</style>
