<template>
    <Dialog v-model:visible="display" header="Consultar Fundamentação Legal" :maximizable="true" :modal="true"
        :style="{ width: '1200px', minHeight: '300px' }">
        <Dialog header="Aviso" v-model:visible="aviso" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
        <div class="h-full" style="min-height: 300px">
            <div class="flex align-items-center">
                <div>
                    <label for="rh137_sequencial">Sequencial: </label>
                    <InputNumber inputId="rh137_sequencial" v-model="form.rh137_sequencial" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-3">
                    <label for="rh137_instituicao">Instituição: </label>
                    <InputNumber inputId="rh137_instituicao" v-model="form.rh137_instituicao" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-3">
                    <label for="rh137_numero">Numero: </label>
                    <InputNumber inputId="rh137_numero" v-model="form.rh137_numero" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-1">
                    <label for="rh137_descricao">Descrição: </label>
                    <InputText inputId="rh137_descricao" class="p-inputtext-sm" v-model="form.rh137_descricao" />
                </div>
                <div class="ml-3">
                    <Button label="Secondary" class="p-button-sm m-3" @click="carregarDados()">Pesquisar</Button>
                    <Button label="Secondary" class="p-button-sm p-button-info m-3" @click="limpar">Limpar</Button>
                </div>
            </div>
            <DataTable :value="resultados" :loading="loading" @rowSelect="onRowSelect" :rowHover="true"
                selectionMode="single" responsiveLayout="scroll" :rows="10" :paginator="true">
                <Column field="rh137_sequencial" header="Sequencial" />
                <Column field="rh137_instituicao" header="Instituição" />
                <Column field="rh137_descricao" header="Descrição" />
                <Column field="rh137_numero" header="Numero" />
                <template #empty>
                    Nenhum registro encontrado
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
<script>

export default {
    name: 'DialogRhFundamentacaoLegal',
    components: {},
    data() {
        return {
            msg: null,
            aviso: false,
            display: false,
            loading: false,
            resultados: [],
            form: {
                rh137_sequencial: null,
                rh137_instituicao: null,
                rh137_descricao: null,
                rh137_numero: null
            },
        }
    },
    methods: {
        carregarDados() {
            const data = Object.assign(this.form, {})
            data.rh137_instituicao = this.form.rh137_instituicao;
            data.rh137_descricao = this.form.rh137_descricao;
            data.rh137_numero = this.form.rh137_numero;
            data.rh137_sequencial = this.form.rh137_sequencial;
            this.loading = true;
            window.axios.post('v4/api/recursos-humanos/pessoal/rhfundlegal/list', data)
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
            this.form.rh137_instituicao = null
            this.form.rh137_descricao = null
            this.form.rh137_numero = null
            this.form.rh137_sequencial = null
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
