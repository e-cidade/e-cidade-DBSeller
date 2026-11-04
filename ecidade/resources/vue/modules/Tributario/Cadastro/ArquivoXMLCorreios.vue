<template>
    <BlockUI :blocked="loading" class="h-full" :fullscreen="true">
        <section class="pt-4 px-3">
            <div class="m-auto" style="max-width: 1000px">
                <Panel>
                    <template #header>
                        <b>Pesquisar</b>
                    </template>
                    <div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 180px">
                                <label>Codigo da Emiss&atilde;o:</label>
                            </div>
                            <div class="col">
                                <Calendar style="width: 180px" disabled class="p-inputtext-sm" v-model="form.codigo" />
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 180px">
                                <label>Data Gera&ccedil;&atilde;o:</label>
                            </div>
                            <div class="col">
                                <Calendar style="width: 180px" class="p-inputtext-sm" v-model="form.data_geracao" />
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 180px">
                                <label>Gera&ccedil;&atilde;o Geral Ecarta:</label>
                            </div>
                            <div class="col">
                                <Dropdown
                                    :options="remessa"
                                    optionLabel="tr13_codigo"
                                    optionValue="tr13_codigo"
                                    v-model="form.emissao_geral_ecarta"
                                    placeholder=""
                                    style="width: 180px"
                                />
                            </div>
                        </div>
                        <div class="grid">
                            <div class="col-fixed pt-3" style="width: 180px">
                                <label>Arquivo Complementaris:</label>
                            </div>
                            <div class="col">
                                <InputSwitch v-model="form.arquivo_complementaris" class="p-inputtext-sm" />
                            </div>
                        </div>
                    </div>
                </Panel>
                <div class="border-1 border-500 p-4 mt-4">
                    <Button :disabled="!validationEmitir()" @click="emitir" class="mr-3">
                        Emitir
                    </Button>
                    <Button @click="consultar" class="mr-3 p-button-info">
                        Consultar
                    </Button>
                    <Button @click="reset" class="p-button-danger">
                        Limpar
                    </Button>
                </div>
            </div>
        </section>
        <DialogArquivoXMLEcarta ref="dialog_arquivo_xml_ecarta" />
    </BlockUI>
</template>

<script>
import DialogArquivoXMLEcarta from './components/DialogArquivoXMLEcarta.vue'

export default {
    name: 'ArquivoXMLCorreios',
    components: {
        DialogArquivoXMLEcarta
    },
    mounted() {
        this.form.codigo = window.uuid()

        this.getListaEmissaoGeralEcarta()
    },
    data() {
        return {
            loading: false,
            form: {
                codigo: '',
                data_geracao: '',
                emissao_geral_ecarta: '',
                arquivo_complementaris: true
            },
            remessa: []
        }
    },
    methods: {
        validationEmitir() {
            if (
                this.form.codigo.length <= 0 ||
                this.form.data_geracao.length <= 0 ||
                this.form.emissao_geral_ecarta <= 0
            ) {
                return false
            }

            return true
        },
        async getListaEmissaoGeralEcarta() {
            this.loading = true

            await  window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/lista_concluidos_geral')
                .then((res) => this.remessa = res.data.data)
                .catch((error) => console.log(error))
                .finally(() => this.loading = false)
        },
        reset() {
            this.form.codigo                 = window.uuid()
            this.form.data_geracao           = ''
            this.form.emissao_geral_ecarta   = ''
            this.form.arquivo_complementaris = true
        },
        emitir() {
            this.loading = true

            window.axios.post("v4/api/tributario/cadastro/arquivo_xml_correios/emitir", this.form)
                .then((res) => alert(res.data.message))
                .catch((error) => {
                    console.log(error.response)
                })
                .finally(() => {
                    this.loading = false
                    this.reset()
                })
        },
        consultar() {
            this.$refs['dialog_arquivo_xml_ecarta'].openModal()
        }
    }
}
</script>