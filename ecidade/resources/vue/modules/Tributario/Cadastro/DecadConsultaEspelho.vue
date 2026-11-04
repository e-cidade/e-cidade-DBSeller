<template>
    <ModalLoading :isLoading="loading" />
    <section class="container">
        <Panel>
            <template #header>
                <b>Consultar Espelho</b>
            </template>
            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 100px">
                            <label for="cgm">CGM: </label>
                        </div>
                        <div class="col">
                            <InputNumber inputId="cgm" v-model="cgm" mode="decimal" :useGrouping="false" />
                        </div>
                        <div class="col-fixed pt-3">
                            <label for="percentual">Selecione o exercicio: </label>
                        </div>
                        <div class="col">
                            <Dropdown style="width: 246px" v-model="ano" :options="anos" optionLabel="name"
                                optionValue="code" placeholder="" />
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 100px">
                            <label for="matricula">Matricula: </label>
                        </div>
                        <div class="col">
                            <InputNumber inputId="matricula" v-model="matricula" mode="decimal" :useGrouping="false" />
                        </div>
                    </div>
                    <div class="grid  mt-5">
                        <div class="col flex align-items-center justify-content-center">
                            <Button label="Secondary" class="p-button-sm m-3" @click="pesquisar()">Pesquisar</Button>
                            <Button label="Secondary" class="p-button-sm p-button-info m-3"
                                @click="limpar">Limpar</Button>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
        <Dialog header="Aviso" v-model:visible="display"
            :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            :style="{ width: '50vw' }">
            {{ msg }}
        </Dialog>
    </section>
</template>

<style scoped>
.black {
    color: rgb(5, 5, 5);
}
</style>

<script>
import ModalLoading from '../../Components/ModalLoading.vue'
export default {
    name: 'DecadConsultaEspelho',
    components: {
        ModalLoading
    },
    data() {
        return {
            loading: false,
            display: false,
            msg: null,
            cgm: null,
            matricula: null,
            ano: null,
            form: {
                cgm: false,
                matricula: false,
                ano: null
            },
            anos: false
        }
    },
    methods: {
        pesquisar() {
            const data = Object.assign(this.form, {})

            if (this.ano == null) {
                this.msg = "Selecione o Ano"
                this.display = true
                return
            } else if (this.cgm == null && this.matricula == null) {
                this.msg = "Defina o CGM ou a Matricula"
                this.display = true
                return
            }

            data.ano = this.ano;
            data.cgm = this.cgm;
            data.matricula = this.matricula;
            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/espelho', data)
                .then((res) => {
                    const data = res.data.data
                    window.open(window.CurrentWindow.ECIDADE_REQUEST_PATH + data, '', 'height=800, width=600');
                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                })
        },
        limpar() {
            this.matricula = null
            this.cgm = null
            this.selected = null

        }, getAno() {
            const dataAtual = new Date();
            const anoAtual = dataAtual.getFullYear();
            this.ano = anoAtual;
            this.anos = [
                { name: anoAtual, code: anoAtual },
                { name: anoAtual - 1, code: anoAtual - 1 }
            ];
        },
    }, beforeMount() {
        this.getAno()
    },
}
</script>
