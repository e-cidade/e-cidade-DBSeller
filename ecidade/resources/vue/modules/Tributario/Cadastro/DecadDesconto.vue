<template>
    <ModalLoading :isLoading="loading" />
    <section class="container">
        <Panel>
            <template #header>
                <b>Pesquisar</b>
            </template>
            <div class="flex align-items-center justify-content-center m-2" style="width: 100%;">
                <div class="p-1 m-auto">
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Selecione o exercicio: </label>
                        </div>
                        <div class="col">
                            <Dropdown style="width: 246px" :disabled="!isActive" v-model="ano" :options="anos"
                                optionLabel="name" optionValue="code" placeholder="" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Data Inicial: </label>
                        </div>
                        <div class="col">
                            <Calendar class="p-inputtext-sm" :disabled="!isActive" v-model="datainicial"
                                :showIcon="true" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Data Final: </label>
                        </div>
                        <div class="col">
                            <Calendar class="p-inputtext-sm" :disabled="!isActive" v-model="datafinal"
                                :showIcon="true" />
                        </div>
                    </div>
                    <div class="grid mt-5">
                        <div class="col flex align-items-center justify-content-center">
                            <Button label="Secondary" class="p-button-sm m-5" :disabled="!isActive"
                                @click="pesquisar()">Pesquisar</Button>
                            <Button label="Secondary" class="p-button-sm p-button-danger m-5"
                                @click="limpar">Limpar</Button>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>

        <Panel class="mt-5">
            <template #header>
                <b>Lista de dados</b>
            </template>
            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <div class="grid">
                        <div class="col-fixed pt-3">
                            <label for="quantcgm">Quantidade de CGMs: </label>
                        </div>
                        <div class="col">
                            <InputNumber readonly inputId="quantcgm" v-model="quant" mode="decimal"
                                :useGrouping="false" />
                        </div>
                        <div class="col-fixed pt-3">
                            <label for="percentual">Percentual de desconto: </label>
                        </div>
                        <div class="col">
                            <InputNumber readonly inputId="percentual" v-model="perc" mode="decimal" :min="0"
                                :max="100" />
                        </div>
                        <div class="col-fixed pt-3">
                            <label for="percentual">(%)</label>
                        </div>
                    </div>

                    <div class="grid mt-5">
                        <div class="col flex align-items-center justify-content-center">
                            <Button label="Secondary" :disabled="isActive" class="p-button-sm  m-3"
                                @click="visualizar()">Visualizar descontos a processar</Button>
                            <Button label="Secondary" :disabled="isActive" class="p-button-sm p-button-info  m-3"
                                @click="openBasic">Processar
                                descontos
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
        <Panel v-if="processando" class="mt-5">
            <template #header>
                <b>Processamento</b>
            </template>
            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <div class="field col-12 md:col-4">
                        <Knob v-model="value" size="150" readonly valueTemplate="{value}%" />
                    </div>
                </div>
            </div>
        </Panel>
        <Dialog header="Aviso" v-model:visible="display" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
        <Dialog header="Processamento" v-model:visible="displayBasic"
            :breakpoints="{ '960px': '75vw', '640px': '90vw' }" :style="{ width: '50vw' }">
            <p>Deseja efetuar o processamento ?</p>
            <template #footer>
                <Button label="Não" icon="pi pi-times" @click="closeBasic" class="p-button-text" />
                <Button label="Sim" icon="pi pi-check" @click="processar" autofocus />
            </template>
        </Dialog>
    </section>
</template>
<style scoped>

</style>
<script>
import ModalLoading from '../../Components/ModalLoading.vue'
export default {
    name: 'DecadDesconto',
    components: {
        ModalLoading
    },
    props: ['instit'],
    data() {
        return {
            processando: false,
            displayBasic: false,
            value: 0,
            isActive: true,
            loading: false,
            cgms: [],
            display: false,
            msg: null,
            datafinal: null,
            datainicial: null,
            quant: 0,
            perc: 0,
            ano: null,
            form1: {
                datainicial: false,
                datafinal: false,
                ano: null
            },
            anos: null,
        }
    }, methods: {
        pesquisar() {
            const data = Object.assign(this.form1, {})
            if (this.ano == null) {
                this.msg = "Selecione o Ano"
                this.display = true
                return
            } else if (this.datainicial == null) {
                this.msg = "Defina a Data Inicial"
                this.display = true
                return
            }

            if (this.datafinal == null) {
                const d = new Date();
                data.datafinal = d.toISOString().split("T")[0];
            } else {
                data.datafinal = this.datafinal.toISOString().split("T")[0];
            }

            data.ano = this.ano;
            data.datainicial = this.datainicial.toISOString().split("T")[0];
            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/pesquisar', data)
                .then((res) => {
                    this.loading = false;
                    const data = res.data.data
                    this.quant = data.cgms.length
                    this.cgms = data.cgms
                    this.perc = data.perc
                    if (data.cgms.length > 0) {
                        this.isActive = false
                    } else {
                        this.msg = 'Nenhum CGM encontrado'
                        this.display = true
                    }
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                })
        },
        visualizar() {
            const data = Object.assign({
                ano: this.ano,
                cgms: this.cgms,
                perc: this.perc
            }, {})

            if (this.datafinal == null) {
                const d = new Date();
                const dataArray = d.toISOString().split("T")[0].split('-');
                data.datafinal = dataArray[2] + '/' + dataArray[1] + '/' + dataArray[0];
            } else {
                const dataArray = this.datafinal.toISOString().split("T")[0].split('-');
                data.datafinal = dataArray[2] + '/' + dataArray[1] + '/' + dataArray[0];
            }
            const dataArray1 = this.datainicial.toISOString().split("T")[0].split('-');
            data.datainicial = dataArray1[2] + '/' + dataArray1[1] + '/' + dataArray1[0];

            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/desconto', data)
                .then((res) => {
                    this.loading = false;
                    const data = res.data.data
                    window.open(window.CurrentWindow.ECIDADE_REQUEST_PATH + data, '', 'height=800, width=600');
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                })

        },
        processar() {
            const data = Object.assign({
                ano: this.ano,
                perc: this.perc,
                datainicial: this.datainicial.toISOString().split("T")[0],
                DB_instit: this.instit,
                processar: true
            }, {})

            if (this.datafinal == null) {
                const d = new Date();
                data.datafinal = d.toISOString().split("T")[0];
            } else {
                data.datafinal = this.datafinal.toISOString().split("T")[0];
            }

            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/desconto', data)
                .then((res) => {
                    this.loading = false;
                    const message = 'Descontos em Processamento'
                    this.msg = message
                    this.display = true
                    this.processando = true
                    this.value = 0
                    this.closeBasic()
                    this.startInterval()
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                    this.loseBasic()
                })
        },
        getAno() {
            window.axios
                .get('v4/api/tributario/cadastro/decad/ano')
                .then((res) => {
                    const data = res.data
                    this.anos = [
                        { name: data - 1, code: data - 1 },
                        { name: data, code: data }
                    ];
                    this.ano = data
                })
        },
        startInterval: function () {
            var intervalo = setInterval(() => {
                window.axios
                    .get('v4/api/tributario/cadastro/decad/processamento')
                    .then((res) => {
                        const data = res.data.data
                        if (data.length != 0) {
                            this.processando = true;
                            this.value = (data[0].quantidade / data[0].total * 100).toFixed(2);
                            if ((data[0].quantidade / data[0].total * 100).toFixed(2) == 100.00) {
                                this.value = 100
                            }
                        } else {
                            this.processando = false
                            clearInterval(intervalo);
                        }
                    })
            }, 3000);
        },
        getProcessamento() {
            window.setInterval(function () {

            }, 1000);
        },
        limpar() {
            this.isActive = true
            this.quant = 0
            this.perc = 0
            this.cgms = []
            this.datainicial = null
            this.datafinal = null
            this.ano = null
        },
        openBasic() {
            this.displayBasic = true;
        },
        closeBasic() {
            this.displayBasic = false;
        },
    }, beforeMount() {
        this.getAno()
        this.startInterval()
    },
}
</script>
