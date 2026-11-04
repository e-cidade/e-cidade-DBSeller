<template>
    <BlockUI :blocked="loading" class="h-full" :fullscreen="true">
        <section class="pt-4">
            <div class="m-auto px-3" style="max-width: 1000px">
                <Panel>
                    <template #header>
                        <b>Configura&ccedil;&atilde;o Servidor Correios FTP</b>
                    </template>
                    <div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 150px">
                                <label> SSL: </label>
                            </div>
                            <div class="col-fixed" style="width: 200px; height: 29.6px;">
                                <InputSwitch v-model="form.ssl" class="p-inputtext-sm" />
                            </div>
                            <div v-if="this.errors.ssl" class="col-fixed pt-3 text-red-400"> 
                                {{ this.errors['ssl'][0] }}
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 150px">
                                <label> Sevidor (Host): </label>
                            </div>
                            <div class="col-fixed" style="width: 200px">
                                <InputText v-model="form.host" class="p-inputtext-sm" />
                            </div>
                            <div v-if="this.errors.host" class="col-fixed pt-3 text-red-400"> 
                                {{ this.errors['host'][0] }}
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 150px">
                                <label> Porta: </label>
                            </div>
                            <div class="col-fixed" style="width: 200px">
                                <InputNumber mode="decimal" :useGrouping="false" :max="65536" :min="1" v-model="form.port" class="p-inputtext-sm" />
                            </div>
                            <div v-if="this.errors.port" class="col-fixed pt-3 text-red-400"> 
                                {{ this.errors['port'][0] }}
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 150px">
                                <label> Nome do Usu&aacute;rio: </label>
                            </div>
                            <div class="col-fixed" style="width: 200px">
                                <InputText v-model="form.username" class="p-inputtext-sm" />
                            </div>
                            <div v-if="this.errors.username" class="col-fixed pt-3 text-red-400"> 
                                {{ this.errors['username'][0] }}
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 150px">
                                <label> Senha: </label>
                            </div>
                            <div class="col-fixed" style="width: 200px">
                                <Password v-model="form.password" toggleMask class="p-inputtext-sm" />
                            </div>
                            <div v-if="this.errors.password" class="col-fixed pt-3 text-red-400"> 
                                {{ this.errors['password'][0] }}
                            </div>
                        </div>
                    </div>
                </Panel>
                <div class="border-1 border-500 p-4 mt-4">
                    <Button @click="salvar" class="mr-3">
                        Salvar
                    </Button>
                    <Button @click="teste" class="p-button-info">
                        Testar Conex&atilde;o
                    </Button>
                </div>
            </div>
        </section>
    </BlockUI>
</template>

<script>
export default {
    name: 'ArquivoXMLCorreios',
    data() {
        return {
            loading: false,
            errors: [],
            form: {
                ssl: false,
                host: '',
                port: 21,
                username: '',
                password: ''
            }
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        getData() {
            this.loading = true

            window.axios.post('v4/api/configuracao/arquivo_xml_correios/data', this.form)
                .then((res) => {
                    const data = res.data.data 

                    this.form = data
                })
                .catch((error) => alert(error.response.data.message))
                .finally(() =>  this.loading = false)
        },
        salvar() {
            this.loading = true
            this.errors  = {}

            window.axios.post('v4/api/configuracao/arquivo_xml_correios/salvar', this.form)
                .then((res) => alert(res.data.message))
                .catch((error) => {
                    const data = error.response.data

                    if (data.message) {
                        alert(data.message)
                    } else {
                        this.errors = data;
                    }

                })
                .finally(() =>  this.loading = false)
        },
        teste() {
            this.loading = true
            this.errors  = {}

            window.axios.post('v4/api/configuracao/arquivo_xml_correios/teste', this.form)
                .then((res) => alert(res.data.message))
                .catch((error) => {
                    const data = error.response.data

                    if (data.message) {
                        alert(data.message)
                    } else {
                        this.errors = data;
                    }

                })
                .finally(() =>  this.loading = false)
        }
    }
}
</script>