<template>
    <Dialog 
      header="Grupo de Débito"
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
            :rows="8"
            showGridlines
            :paginator="true"
            filterDisplay="menu"
            v-model:filters="filters"
            :loading="loading"
            v-model::selection="select"
            selectionMode="single"
            dataKey="k03_tipo"
            @rowSelect="onRowSelect"
            :globalFilterFields="['k03_descr', 'k03_parcelamento', 'k03_tipo', 'k03_permparc', 'k03_parcano']"
        >
            <template #empty>
                Nenhum grupo de débito foi encontrado
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
            <Column field="k03_tipo" :sortable="true" header="Grupo de Débito" />
            <Column field="k03_descr" :sortable="true" header="Descrição do TIpo de Débito" />
            <Column field="k03_parcano" header="Se parcela débito somente no ano atual ou não">
                <template #body="slotProps">
                    {{ (slotProps.data.k03_parcano) ? 'SIM' : 'NÃO' }}
                </template>
            </Column>
            <Column field="k03_parcelamento" header="Se tipo de débito é parcelamento ou não">
                <template #body="slotProps">
                    {{ (slotProps.data.k03_parcelamento) ? 'SIM' : 'NÃO' }}
                </template>
            </Column>
            <Column field="k03_permparc" header="Se permite parcelar este tipo de débito">
                <template #body="slotProps">
                    {{ (slotProps.data.k03_permparc) ? 'SIM' : 'NÃO' }}
                </template>
            </Column>
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

            await window.axios.get('v4/api/tributario/cadastro/grupo-debito/listar')
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
        async verifyGrupoDebito(number) {
            return await window.axios
                .get(`v4/api/tributario/cadastro/grupo-debito/search/${number}`)
        }
    }
}
</script>

<style scoped>

.padding-custom  {
    width: 250px;
}

</style>