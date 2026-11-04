<script setup>
    import { useToast } from "primevue/usetoast";

    import { ref, onMounted  } from 'vue'
    const apiurl = 'v4/api/recursos-humanos/pessoal/tabelaprevidencia/';
    const loading = ref(false);
    const toast = useToast();
    const tabelasPrevidencia = ref({});

    const selectedPrevidencia = ref();
    const previdencia = ref([]);
    /**
     * Methods
     */
    const getTabelaPrevidencia = async () => {
        const action = apiurl + 'list';
        loading.value = true;
        try {
            const response = await axios.get(action);
            previdencia.value = response.data
        } catch (error) {
            console.log(error);
            // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados.', life: 5000 });
        }

        loading.value = false;
    }

    /**
     * Hooksl
    */
    onMounted(() => {
        getTabelaPrevidencia();
    });

    defineExpose({
        tabelasPrevidencia,
    });
</script>
<template>
    <Dropdown value="codigo" v-model="selectedPrevidencia" :options="previdencia.data" optionLabel="descricao" placeholder="Selecione uma Previdência"/>
</template>