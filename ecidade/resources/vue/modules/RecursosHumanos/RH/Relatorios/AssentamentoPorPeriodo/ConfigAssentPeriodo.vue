<script setup>
    import { ref, onMounted } from 'vue';
    import Toast from 'primevue/toast';
    import { useToast } from "primevue/usetoast";
    import ModalLoading from "../../../../Components/ModalLoading";

    /**
     * Utils
     */
    const apiurl  = 'v4/api/recursos-humanos/rh/relatorios/configassentperiodo/';
    const toast   = useToast();
    const loading = ref(false);

    /**
     * Structs
     */
    const configStruct = {
        rh512_filtroadicionais: false,
        rh512_filtroassentdepart: false,
        rh512_assentferias: []
    }

    /**
     * Data
     */
    const config = ref({});
    const institDescr = ref('');
    const assentamentos = ref([]);

    /**
     * Initialize
     */
    config.value = configStruct;

    /**
     * Methods
     */
    const getConfig = async () => {
        const action = apiurl + 'getConfig';
        loading.value = true;

        try {
            const response = await axios.get(action);
            const rdata    = response.data;
            const data     = rdata.data.config ?? false;

            institDescr.value = rdata.data.institDescr;
            fillConfig(data);
        } catch (error) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados', life: 5000 });
        }

        loading.value = false;
    }

    const fillConfig = data => {
        if (data) {
            const props = Object.keys(data);

            // filtro adicionais
            if (props.indexOf('rh512_filtroadicionais')) {
                configStruct.rh512_filtroadicionais = data.rh512_filtroadicionais;
            }

            // filtro assentamento do departamento
            if (props.indexOf('rh512_filtroassentdepart')) {
                configStruct.rh512_filtroassentdepart = data.rh512_filtroassentdepart;
            }

            // assentamentos de ferias
            if (props.indexOf('rh512_assentferias')) {
                configStruct.rh512_assentferias = data.rh512_assentferias;
            }
        }

        config.value = configStruct;
    }

    const sendForm = async () => {
        const action = apiurl + 'saveConfig';
        loading.value = true;

        try {
            const response = await axios.post(action, config.value);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Configurações salvas', life: 5000 });
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível salvar os dados', life: 5000 });
        }

        loading.value = false;
    }

    const getAssentamentos = async () => {
        const action = apiurl + 'getAssentamentos';
        try {
            const response = await axios.get(action);
            const rdata    = response.data;
            assentamentos.value = rdata.data.assentamentos ?? [];
        } catch (error) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível buscar os assentamentos', life: 5000 });
        }
    }

    /**
     * Hooks
     */
    onMounted(() => {
        getAssentamentos();
        getConfig();
    });
</script>

<template>
    <ModalLoading :is-loading="loading"/>
    <Toast />

    <div class="container mt-5">
        <Panel header="Configurações">
            <!-- intituição -->
            <div class="field grid">
                <label for="instit" class="col-3" size="20">Instituição: </label>
                <div class="col-9">
                    <InputText id="instit" v-model="institDescr" size="50" disabled/>
                </div>
            </div>

            <!-- filtros adicionais -->
            <div class="field grid">
                <label for="filtroadicionais" class="col-3">Filtros Adicionais: </label>
                <div class="col-9">
                    <Checkbox v-model="config.rh512_filtroadicionais" inputId="filtroadicionais" binary/>
                    <small class="ml-2 text-800 text-xs">Habilita filtro de seleção e Lotação.</small>
                </div>
            </div>

            <div v-if="config.rh512_filtroadicionais">
                <!-- assentamentos de ferias -->
                <div class="fileld grid">
                    <label for="assentamentosferias" class="col-3">Assentamentos de férias: </label>
                    <div class="col-9">
                        <MultiSelect v-model="config.rh512_assentferias"
                            :options="assentamentos" filter
                            optionLabel="h12_descr"
                            optionValue="h12_codigo"
                            class="w-full md:w-20rem"
                        />
                    </div>
                </div>

                <!-- Exibir apenas assentamentos do departamento -->
                <div class="field grid">
                    <label for="assentdepart" class="col-3">Assentamentos do departamento: </label>
                    <div class="col-9">
                        <Checkbox v-model="config.rh512_filtroassentdepart" inputId="assentdepart" binary/>
                        <small class="ml-2 text-800 text-xs">Exibir apenas assentamentos do departamento</small>
                    </div>
                </div>
            </div>

            <Button label="Salvar" class="mt-4" @click="sendForm"/>
        </Panel>
    </div>
</template>
