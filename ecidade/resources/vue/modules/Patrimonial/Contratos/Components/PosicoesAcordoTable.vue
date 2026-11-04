<template>
    <DataTable :value="dadosTabela"
               :style="{ 'height': '14em' }"
               scrollable
               scrollHeight="14em"
               sortMode="multiple"
               :onRowDblclick="selecionarLinha">
        <Column field="ac26_sequencial"
                sortable
                header="Código"
                style="width: 20%">
        </Column>
        <Column field="ac26_numeroaditamento"
                sortable
                header="Número do Termo"
                style="width: 20%">
        </Column>
        <Column field="acordotipo"
                sortable
                header="Tipo"
                style="width: 20%">
        </Column>
        <Column field="ac26_data"
                sortable
                header="Data"
                style="width: 20%">
        </Column>
        <Column field="ac26_emergencial"
                sortable
                header="Emergencial"
                style="width: 20%">
        </Column>
    </DataTable>
</template>

<script>
import { ref, computed } from 'vue'
import DataTable from 'primevue/datatable'

export default {
    name: 'PosicoesAcordoTable',
    emits: ['linhaSelecionada'],
    components: {
        DataTable
    },
    props: {
        dadosTabela: {
            type: Array,
            required: true
        }
    },
    setup(props , { emit }) {
        function selecionarLinha(event) {
            emit('linhaSelecionada',event.data);
        }

        const dadosTabela = computed(() => {
            return props.dadosTabela
        })


        return {
            dadosTabela,
            selecionarLinha,
        }
    }
}
</script>

