<template>
    <Dialog 
      header="Tipo de Débito"
      :maximizable="true"
      :modal="true"
      :style="{ width: '1000px' }"
      position="top"
      v-model:visible="display"
    >
      <section style="width: 100%;" class="m-auto">
        <DataTable 
            :value="data" 
            responsiveLayout="scroll" 
            :rowHover="true"
            :rows="10"
            showGridlines
            :paginator="true"
            filterDisplay="menu"
            v-model:filters="filters"
            :loading="loading"
            v-model::selection="select"
            selectionMode="single"
            dataKey="k00_tipo"
            @rowSelect="onRowSelect"
            :globalFilterFields="['k00_descr', 'k00_tipo', 'k03_tipo', 'k00_emrec']"
        >
            <template #empty>
                Nenhum tipo de débito foi encontrado
            </template>
            <template #header>
                <div class="flex flex-column">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText
                            mode="decimal"
                            :useGrouping="false"
                            v-model="filters['global'].value" 
                            placeholder="Pesquisar"
                            class="padding-custom p-inputtext-sm"
                        />
                    </span>
                </div>
            </template>
            <Column field="k00_tipo" :sortable="true" header="Tipo de Débito" />
            <Column field="k00_descr" :sortable="true" header="Tipo de Débito" />
            <Column field="k00_emrec" :sortable="true" header="Emite recibo e carnê">
                <template #body="slotProps">
                    {{ (slotProps.data.k00_emrec) ? 'SIM' : 'NÃO' }}
                </template>
            </Column>
            <Column field="k03_tipo" :sortable="true" header="Cadtipo" />
        </DataTable>
      </section>
    </Dialog>
</template>

<script>
import { ref  } from 'vue'
import { FilterMatchMode } from 'primevue/api'

export default {
    name: 'DialogTipoDebito',
    data() {
        return {
            loading: false,
            select: null,
            display: false,
            filters: ref({
                'global': { value: null, matchMode: FilterMatchMode.STARTS_WITH }
            }),
            data: []
        }
    },
    methods: {
        openModal() {
            this.display = true
            this.loadingTipoDebito()
        },
        async loadingTipoDebito() {
            this.loading = true
            this.data    = []

            await window.axios.get('v4/api/tributario/arrecadacao/tipo-debito/listar')
                .then((res) => {
                    const data = res.data.data
                   
                    this.data = data;
                })
                .finally(() => this.loading = false)
        },
        onRowSelect(event) {
            this.data    = []
            this.display = false

            this.$emit('selectRow', event.data)
        },
        async verifyTipoDebito(number) {
            return await window.axios
                .get(`v4/api/tributario/arrecadacao/tipo-debito/search/${number}`)
        }
    }
}
</script>

<style scoped>

.padding-custom  {
    width: 250px;
}

</style>