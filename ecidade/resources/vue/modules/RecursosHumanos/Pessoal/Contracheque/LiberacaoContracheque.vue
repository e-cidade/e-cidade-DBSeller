<script setup>
    import { ref, onMounted } from 'vue';
    import Toast from 'primevue/toast';
    import { useToast } from "primevue/usetoast";
    import ModalLoading from "../../../Components/ModalLoading";

    /**
     * Utils
     */
    const apiurl  = 'v4/api/recursos-humanos/pessoal/contra-cheques/liberacaoonline/';
    const toast   = useToast();
    const loading = ref(false);
    const configuracoes = ref();
    /**
     * Methods
     */
    const getConfig = async () => {
        const action = apiurl + 'getConfig';
        loading.value = true;

        try {
            const response = await axios.get(action);
            configuracoes.value = response.data
            configuracoes.value.forEach(element => {
                element.atualiza = false;
            });
        } catch (error) {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados', life: 5000 });
        }

        loading.value = false;
    }

    /**
     * Hooks
     */
    onMounted(() => {
        getConfig();
    });

    const salvar = async () => {
        const dadosEnvio = [];
        configuracoes.value.forEach(element => {
            if (element.atualiza == true) {
                dadosEnvio.push(element);
                element.atualiza = false;
            }
        });

        if (dadosEnvio.length > 0) {
            try {
                const action = apiurl + 'saveConfig';
                loading.value = true;
                const response = await axios.post(action, {"configuracoes": dadosEnvio});
                toast.add({ severity: 'success', summary: 'Aviso', detail: 'Liberações atualizadas com sucesso.', life: 5000 });
                
            } catch (error) {
                toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi salvar os dados', life: 5000 });
            }
            loading.value = false;
        } else {
            toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Nenhuma alteração de configuração realizada.', life: 5000 });
        }
    }

    const alteraCheckBox = (elemento, campo) => {
        elemento[campo] = !elemento[campo];
        elemento.atualiza = true;
    }
</script>

<template>
    <ModalLoading :is-loading="loading"/>
    <Toast />

    <div class="container mt-5">
        <div style="display: block;">
            <Panel header="Liberação de Contracheques">
                <div class="grid">
                    <div style="width: 100%; margin: 0 auto; height: 450px;">
                        <div class="p-fluid grid" >
                            <DataTable 
                                :value="configuracoes" 
                                scrollable 
                                scrollHeight="420px" 
                                showGridlines 
                                style="width: 100%"
                                :globalFilterFields="['ano']" 
                                paginator 
                                :rows="10" 
                                dataKey="id" 
                                removableSort
                            >
                                <Column field="codigo" class="text-center" hidden header="Código">
                                </Column>
                                <Column field="ano" sortable class="text-center" header="Ano">
                                </Column>
                                <Column field="mes" sortable class="text-center" header="Mês">
                                </Column>
                                <Column field="salario" class="text-center" header="Salário">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.salario, 'pi-times-circle text-red-400': !data.salario }"></i>
                                    </template>
                                </Column>
                                <Column field="rescisao" class="text-center" header="Rescisão">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.rescisao, 'pi-times-circle text-red-400': !data.rescisao }"></i>
                                    </template>
                                </Column>
                                <Column field="complementar" class="text-center" header="Complementar">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.complementar, 'pi-times-circle text-red-400': !data.complementar }"></i>
                                    </template>
                                </Column>
                                <Column field="decimo" class="text-center" header="Décimo">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.decimo, 'pi-times-circle text-red-400': !data.decimo }"></i>
                                    </template>
                                </Column>
                                <Column field="adiantamento" class="text-center" header="Adiantamento">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.adiantamento, 'pi-times-circle text-red-400': !data.adiantamento }"></i>
                                    </template>
                                </Column>
                                <Column @click="alteraCheckBox(data, field)" field="suplementar" class="text-center" header="Suplementar">
                                    <template #body="{ data, field }">
                                        <i @click="alteraCheckBox(data, field)" class="pi" :class="{ 'pi-check-circle text-green-500': data.suplementar, 'pi-times-circle text-red-400': !data.suplementar }"></i>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>
        <div>
            <Button label="Salvar" class="mt-4" @click="salvar"/>
        </div>
    </div>
</template>
