<template>
    <section class="container">
        <Panel :toggleable="true">
            <template #header>
                <b>Cálculo Geral</b>
            </template>
            <div>
                <div class="grid">
                    <div class="col-fixed pt-3" style="width: 180px">
                        <label>Calcular IPTU Quitado:</label>
                    </div>
                    <div class="col pt-3">
                        <InputSwitch v-model="form.quitado" />
                    </div>
                </div>
                <div class="grid">
                    <div class="col-fixed pt-3" style="width: 180px">
                        <label>Calcular Financeiro:</label>
                    </div>
                    <div class="col pt-3">
                        <InputSwitch v-model="form.financeiro" />
                    </div>
                </div>
                <div class="grid">
                    <div class="col-fixed pt-3" style="width: 180px">
                        <label>Ano:</label>
                    </div>
                    <div class="col">
                        <Dropdown placeholder="Selecione o ano" class="p-inputtext-sm" :options="optionsAno"
                            v-model="form.ano" optionValue="ano" style="width: 184.6px" optionLabel="ano" />
                    </div>
                </div>
            </div>
        </Panel>
        <div class="border-1 border-500 p-4 mt-4">
            <Button :disabled="!calcular()" class="p-button-sm mr-2" @click="emitirCalculo">
                Calcular
            </Button>
            <Button @click="interromperCalc($event)" class="p-button-sm mr-2 p-button-danger" v-if="hasCalculo">
                Interromper Calcular
            </Button>
            <ConfirmPopup group="interromperCalc"></ConfirmPopup>
            <div v-if="hasCalculo && !this.dataCalculo.processo" class="pt-3">
                Um processo de cálculo geral de IPTU está em andamento:<br>
                - Codigo: {{ this.dataCalculo.j27_codigo }}<br>
                - Total de Processos: {{ this.dataCalculo.jobs }}<br>
                - Em Processo: {{ this.dataCalculo.emprocesso }}<br>
                - Falhas: {{ this.dataCalculo.falhas }}<br>
                - Concluidos: {{ this.dataCalculo.concluidos }}<br>
                - Status: {{ this.dataCalculo.status }}%<br><br>

                <ProgressBar style="height: 30px" :value="this.dataCalculo?.status ?? 0">
                    {{ this.dataCalculo?.status ?? 0 }}
                </ProgressBar>
            </div>
            <div class="mt-3" v-else-if="hasCalculo && this.dataCalculo.processo">
                Carregando matriculas para o calculo geral...<br />
                <span class="text-400" style="font-size: 8px;">
                    Esse processo pode demorar alguns minutos,
                    não há necessidade de manter essa janela aberta.
                </span>
            </div>
        </div>
        <Dialog header="Aviso" :modal="true" v-model:visible="display">
            <p class="text-center">
                {{ msg }}
            </p>
            <template v-if="data.length > 0">
                <div class="mt-3">
                    <hr />
                    <div v-for="(msg, index) in data" :key="index" class="border-bottom-1 py-3">
                        {{ msg }}
                    </div>
                </div>
            </template>
            <template #footer>
                <Button label="Ok" class="p-button-text" @click="() => display = false" />
            </template>
        </Dialog>
        <BlockUI :blocked="loading" :fullScreen="true"></BlockUI>
    </section>
</template>

<script>
import ConfirmPopup from "primevue/confirmpopup";

export default {
    name: 'CalculoGeralIPUT',
    components: { ConfirmPopup },
    created() {
        this.loading = true;
        this.hasCalculo = false;

        this.mountedAnos();
        this.getCalculo();

        this.loading = false;
    },
    data() {
        return {
            update: null,
            loading: false,
            hasCalculo: false,
            loop: null,
            dataCalculo: {},
            msg: '',
            optionsAno: [],
            data: [],
            display: false,
            form: {
                quitado: false,
                financeiro: false,
                ano: null
            }
        }
    },
    methods: {
        interromperCalc(event) {
            console.log(this.$confirm)
            this.$confirm.require({
                target: event.currentTarget,
                message: 'Tem certeza de que deseja continuar?',
                icon: 'pi pi-exclamation-triangle',
                group: 'interromperCalc',
                accept: () => {
                    this.loading = true;

                    window.axios
                        .get('v4/api/tributario/cadastro/calculo_iput/interromper_calc')
                        .then(async (res) => {
                            const data = res.data

                            this.msg = data.message

                            if (this.loop !== null) {
                                clearInterval(this.loop)
                                this.loop = null
                                this.hasCalculo = false
                            }
                            await this.getCalculo()
                        })
                        .finally(() => {
                            this.loading = false
                            this.display = true
                        })
                }
            })
        },
        async getCalculo() {
            await window.axios
                .get('v4/api/tributario/cadastro/calculo_iput/calculo_geral')
                .then((res) => {
                    const data = res.data.data;

                    this.hasCalculo = data.calculando;

                    if (this.hasCalculo) {
                        this.dataCalculo = data.data;
                        if (this.loop == null) {
                            this.startLoop()
                        }

                    } else {
                        if (this.loop != null) {
                            clearInterval(this.loop)
                            this.loop = null
                        }

                        this.dataCalculo = {}
                    }
                });
        },
        mountedAnos() {
            const anos = window.anos ?? [];

            if (anos.length > 0) {
                for (let index in anos) {
                    let ano = anos[index];

                    this.optionsAno.push({ ano });
                }
            }
        },
        calcular() {
            if (this.form.ano == null) {
                return false;
            }

            if (this.hasCalculo) {
                return false;
            }

            return true;
        },
        emitirCalculo() {
            const data = Object.assign(this.form, {});

            this.loading = true;
            this.msg = '';
            this.data = [];
            data.quitado = (data.quitado) ? 1 : 0;
            data.financeiro = (data.financeiro) ? 1 : 0;

            window.axios
                .post('v4/api/tributario/cadastro/calculo_iput/emitir', data)
                .then((res) => {
                    const data = res.data.data

                    this.msg = res.data.message
                })
                .catch((error) => {
                    const res = error.response;
                    this.msg = res.data.message;

                    if (res.status == 400) {
                        this.data = res.data.data;
                        this.display = true;
                    }
                })
                .finally(() => {
                    this.loading = false
                    this.display = true;
                    this.getCalculo();
                });
        },
        startLoop() {
            this.loop = setInterval(() => this.getCalculo(), 10000);
        }
    }
}
</script>