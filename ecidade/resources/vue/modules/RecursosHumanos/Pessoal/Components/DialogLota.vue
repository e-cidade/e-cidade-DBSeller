<template>
    <Dialog v-model:visible="display" header="Consultar Lotações" :maximizable="true" :modal="true"
        :style="{ width: '1200px', minHeight: '300px' }">
        <Dialog header="Aviso" v-model:visible="aviso" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
        <div class="h-full" style="min-height: 300px">
            <div class="flex align-items-center">
                <div class="ml-3">
                    <label for="r70_codigo">Codigo: </label>
                    <InputNumber inputId="r70_codigo" v-model="form.r70_codigo" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-1">
                    <label for="r70_descr">Descrição: </label>
                    <InputText class="p-inputtext-sm" inputId="r70_descr" v-model="form.r70_descr" />
                </div>
                <div class="ml-3">
                    <Button label="Secondary" class="p-button-sm m-3" @click="carregarDados()">Pesquisar</Button>
                    <Button label="Secondary" class="p-button-sm p-button-info m-3" @click="limpar">Limpar</Button>
                </div>
            </div>
            <DataTable :value="resultados" :loading="loading" @rowSelect="onRowSelect" :rowHover="true"
                selectionMode="single" responsiveLayout="scroll" :rows="10" :paginator="true">
                <Column field="r70_instit" header="Instituição" />
                <Column field="r70_codigo" header="Codigo" />
                <Column field="r70_descr" header="Descrição" />
                <template #empty>
                    Nenhum registro encontrado
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
<script>

export default {
    name: 'DialogRhLota',
    components: {},
    data() {
        return {
            msg: null,
            aviso: false,
            display: false,
            loading: false,
            resultados: [],
            form: {
                r70_instit: null,
                r70_descr: null,
                r70_codigo: null
            },
        }
    },
    methods: {
        carregarDados() {
            const data = Object.assign(this.form, {})
            data.r70_instit = this.form.r70_instit;
            data.r70_descr = this.form.r70_descr;
            data.r70_codigo = this.form.r70_codigo;
            this.loading = true;
            window.axios.post('v4/api/recursos-humanos/pessoal/rhlota/list', data)
                .then((res) => {
                    const data = res.data.data;
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    }
                    this.resultados = data;
                    this.loading = false
                })
                .catch((error) => {
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },
        limpar() {
            this.form.r70_descr = null
            this.form.r70_instit = null
            this.form.r70_codigo = null
        },
        openModal() {
            this.display = true
        },
        onRowSelect(event) {
            this.data = []
            this.display = false

            this.$emit('selectRow', event.data)
        },
    }, async beforeMount() {
        this.carregarDados()
    }
}
</script>
