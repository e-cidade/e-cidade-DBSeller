<template>
    <Dialog v-model:visible="display" header="Consultar CGM" :maximizable="true" :modal="true"
        :style="{ width: '1200px', minHeight: '300px' }">
        <Dialog header="Aviso" v-model:visible="aviso" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
        <div class="h-full" style="min-height: 300px">
            <div class="flex align-items-center">
                <div>
                    <label for="cgm">CGM: </label>
                    <InputNumber inputId="cgm" v-model="cgm" mode="decimal" :useGrouping="false" />
                </div>
                <div class="ml-3">
                    <label for="percentual">Exercicio: </label>
                </div>
                <div class="ml-1">
                    <Dropdown style="width: 100px" v-model="ano" :options="anos" optionLabel="name" optionValue="code"
                        placeholder="" />
                </div>
                <div class="ml-3">
                    <Button label="Secondary" class="p-button-sm m-3" @click="carregarDados()">Pesquisar</Button>
                    <Button label="Secondary" class="p-button-sm p-button-info m-3" @click="limpar">Limpar</Button>
                </div>
            </div>
            <DataTable :value="resultados" :loading="loading" @rowSelect="onRowSelect" :rowHover="true"
                selectionMode="single" responsiveLayout="scroll" :rows="10" :paginator="true">
                <Column field="matricula" header="Matricula" />
                <Column field="cpf_cnpj" header="CPF/CNPJ" />
                <Column field="cgm" header="CGM" />
                <Column field="nome" header="Nome" />
                <Column field="protocolo" header="Protocolo" />
                <Column field="situacao" header="Situação"></Column>
                <Column field="valor_antes" header="Valor Antes"></Column>
                <Column field="valor_acressido" header="Valor Acrescido"></Column>
                <Column field="data_cadastro" header="Data do Cadastro"></Column>
                <template #empty>
                    Nenhum registro encontrado
                </template>
            </DataTable>
        </div>
    </Dialog>
</template>
<script>

export default {
    name: 'DialogConsultaCgmEspelhoDECAD',
    components: {  },
    data() {
        return {
            msg: null,
            aviso: false,
            display: false,
            loading: false,
            resultados: [],
            ano: null,
            cgm: null,
            form: {
                cgm: null,
                ano: null
            },
            anos: false
        }
    },
    methods: {
        carregarDados() {
            const data = Object.assign(this.form, {})
            data.ano = this.ano;
            data.cgm = this.cgm;
            this.loading = true;
            window.axios.post('v4/api/tributario/cadastro/decad/listcgm', data)
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
            this.cgm = null
            this.resultados = []
        },
        openModal() {
            this.display = true
        }, getAno() {
            const dataAtual = new Date();
            const anoAtual = dataAtual.getFullYear();
            this.ano = anoAtual;
            this.anos = [
                { name: anoAtual, code: anoAtual },
                { name: anoAtual - 1, code: anoAtual - 1 }
            ];
        },
        onRowSelect(event) {
            this.data = []
            this.display = false

            this.$emit('selectRow', event.data)
        },
    }, async beforeMount() {
        await this.getAno()
        this.carregarDados()
    }
}
</script>