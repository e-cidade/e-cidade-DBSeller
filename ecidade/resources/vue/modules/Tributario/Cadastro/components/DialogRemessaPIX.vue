<template>
    <BlockUI v-model="loading">
        <Dialog
            header="Consulta de Remessa PIX"
            :maximizable="true"
            :modal="true"
            :style="{ width: '1000px', minHeight: '300px' }"
            v-model:visible="display"
            @hide="removerLoading"
        >
            <section style="width: 100%; height: 100%; min-height: 300px" class="m-auto">
                <DataTable :value="remessa" :rows="10" :loading="loading">
                    <Column header="Cod. Requisição" name="codigo" />
                    <Column header="data" name="data" />
                </DataTable>
            </section>
            <template #footer>
                <Button
                    class="p-button-sm p-button-outlined"
                    @click="display = false"
                >
                    Fechar
                </Button>
            </template>
        </Dialog>
    </BlockUI>
</template>

<script>
    export default {
        name: 'DialogRmessaPIX',
        data() {
            return {
                display: false,
                loading: false,
                remessa: []
            }
        },
        methods: {
            openModal() {
                this.display = !this.display
            }
        }
    }
</script>