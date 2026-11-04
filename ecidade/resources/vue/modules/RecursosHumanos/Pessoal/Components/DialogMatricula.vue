<template>
    <Dialog v-model:visible="display" header="Consultar Matrículas" :maximizable="true" :modal="true"
        :style="{ width: '1200px', minHeight: '300px' }">
        <Dialog header="Aviso" v-model:visible="aviso" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
        <div class="h-full" style="min-height: 300px">
            <div class="flex align-items-center">
                <div>
                    <label for="pesquisa">Matricula: </label>
                    <InputText class="p-inputtext-sm" placeholder="Pesquisa" inputId="pesquisa" v-model="form.rh01_regist" />
                </div>
                <div class="ml-3">
                    <label for="instit">CGM: </label>
                    <InputNumber inputId="instit" v-model="form.rh01_numcgm" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-1">
                    <label for="descricao">Nome: </label>
                    <InputText class="p-inputtext-sm" id="cargo" v-model="form.z01_nome" />
                </div>
                <div class="ml-3">
                    <Button label="Secondary" class="p-button-sm m-3" @click="carregarDados()">Pesquisar</Button>
                    <Button label="Secondary" class="p-button-sm p-button-info m-3" @click="limpar">Limpar</Button>
                </div>
            </div>
            <DataTable :value="resultados" :loading="loading" @rowSelect="onRowSelect" :rowHover="true"
                selectionMode="single" responsiveLayout="scroll" :rows="10" :paginator="true">
                <Column field="rh01_regist" header="Matricula" />
                <Column field="rh01_numcgm" header="CGM" />
                <Column field="z01_nome" header="Nome" />
                <template #empty>
                    Nenhum registro encontrado
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
<script>

export default {
    name: 'DialogMatricula',
    components: {},
    data() {
        return {
            msg: null,
            aviso: false,
            display: false,
            loading: false,
            resultados: [],
            form: {
                rh01_regist: null,
                rh01_numcgm: null,
                z01_nome: null
            },
            rh01_regist: null,
            rh01_numcgm: null,
            z01_nome: null
        }
    },
    methods: {
        carregarDados() {
            const data = Object.assign(this.form, {})
            this.loading = true;
            window.axios.post('v4/api/recursos-humanos/pessoal/rhpessoal/list', data)
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
                    console.log(error);
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },
        limpar() {
            this.form.rh01_regist = null
            this.form.rh01_numcgm = null
            this.form.z01_nome = null
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
